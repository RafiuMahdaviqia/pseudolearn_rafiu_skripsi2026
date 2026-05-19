import re
import os

MIGRATIONS_DIR = os.path.join('database', 'migrations')
SEED_FILE = os.path.join('database', 'seeders', 'data', 'patch_afifah.sql')

def parse_migrations():
    tables = {}
    for fname in os.listdir(MIGRATIONS_DIR):
        path = os.path.join(MIGRATIONS_DIR, fname)
        if not path.endswith('.php'):
            continue
        with open(path, 'r', encoding='utf-8') as f:
            text = f.read()
        # find create blocks
        for m in re.finditer(r"Schema::create\(\s*'([a-zA-Z0-9_]+)'\s*,\s*function \(Blueprint \$table\) \{(.*?)\}\);", text, re.S):
            table = m.group(1)
            body = m.group(2)
            non_nullable = set()
            for line in body.splitlines():
                line = line.strip()
                if not line.startswith('$table->'):
                    continue
                # extract column name
                cm = re.match(r"\$table->\w+\(\s*'([^']+)'", line)
                if not cm:
                    continue
                col = cm.group(1)
                if 'nullable()' in line:
                    continue
                non_nullable.add(col)
            tables[table] = non_nullable
    return tables

def split_values_row(row):
    vals = []
    cur = ''
    in_quote = False
    i = 0
    while i < len(row):
        ch = row[i]
        if ch == "'":
            cur += ch
            i += 1
            # consume until closing quote (handle escaped quotes '')
            while i < len(row):
                cur += row[i]
                if row[i] == "'":
                    # check for doubled quote
                    if i+1 < len(row) and row[i+1] == "'":
                        cur += "'"
                        i += 2
                        continue
                    else:
                        i += 1
                        break
                i += 1
        elif ch == ',' and not in_quote:
            vals.append(cur.strip())
            cur = ''
            i += 1
        else:
            cur += ch
            i += 1
    if cur.strip():
        vals.append(cur.strip())
    return vals

def check_seed(tables):
    problems = []
    insert_re = re.compile(r"INSERT\s+IGNORE\s+INTO\s+`([a-zA-Z0-9_]+)`\s*\(([^)]+)\)\s*VALUES", re.I)
    with open(SEED_FILE, 'r', encoding='utf-8') as f:
        text = f.read()
    parts = re.split(r";\s*\n", text)
    for part in parts:
        part = part.strip()
        if not part:
            continue
        m = insert_re.search(part)
        if not m:
            continue
        table = m.group(1)
        cols = [c.strip().strip('`') for c in m.group(2).split(',')]
        # get values block after VALUES
        vals_block = part[m.end():].strip()
        # split multiple rows
        rows = []
        # simple split on ),( boundaries
        rows_raw = re.split(r"\),\s*\(", vals_block)
        for r in rows_raw:
            r = r.strip()
            if r.startswith('('):
                r = r[1:]
            if r.endswith(')'):
                r = r[:-1]
            rows.append(r)
        for ridx, row in enumerate(rows, start=1):
            values = split_values_row(row)
            for i, val in enumerate(values[:len(cols)]):
                col = cols[i]
                # unquoted NULL
                if val.upper() == 'NULL' and table in tables and col in tables[table]:
                    problems.append((table, col, ridx, row))
    return problems

def main():
    tables = parse_migrations()
    problems = check_seed(tables)
    if not problems:
        print('No NULL-in-NOT-NULL-column problems found in patch_afifah.sql')
        return
    print('Found potential problems:')
    for t,c,r,row in problems:
        print(f"Table {t}: column {c} is NOT NULL but seed row #{r} contains NULL")

if __name__ == '__main__':
    main()
