<?php

namespace App\Services;

class DecoyAnswerService
{
    public function makeDecoyLines(array $lines): array
    {
        $lines = array_values(array_map(function ($line) {
            return is_string($line) ? trim($line) : '';
        }, $lines));

        $count = count($lines);
        if ($count === 0) {
            return [];
        }

        $decoys = [];
        for ($i = 0; $i < $count; $i++) {
            $line = $lines[$i] ?? '';
            $neighborIndex = $count > 1 ? (($i + 1) % $count) : $i;
            $neighbor = $lines[$neighborIndex] ?? '';
            $decoys[] = $this->mutateLine($line, $neighbor);
        }

        return $decoys;
    }

    private function mutateLine(string $line, string $neighbor): string
    {
        $line = trim($line);
        if ($line === '') {
            return $line;
        }

        $words = preg_split('/\s+/', $line, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $neighborWords = preg_split('/\s+/', trim($neighbor), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if (count($words) >= 3) {
            $result = $this->replaceWord($words, $neighborWords);
        } else {
            $result = $this->misspell($line);
        }

        if ($result === $line) {
            $result = $this->forceDifferent($line);
        }

        return $result;
    }

    private function replaceWord(array $words, array $neighborWords): string
    {
        $count = count($words);
        if ($count === 0) {
            return '';
        }

        $index = $count > 1 ? random_int(0, $count - 1) : 0;
        $replacement = $this->pickNeighborWord($neighborWords, $words[$index]);

        if ($replacement !== null) {
            $words[$index] = $replacement;
            return implode(' ', $words);
        }

        return $this->swapWords($words);
    }

    private function pickNeighborWord(array $neighborWords, string $current): ?string
    {
        $candidates = array_values(array_filter($neighborWords, function ($word) use ($current) {
            return $word !== $current;
        }));

        if (empty($candidates)) {
            return null;
        }

        return $candidates[random_int(0, count($candidates) - 1)];
    }

    private function swapWords(array $words): string
    {
        $count = count($words);
        if ($count < 2) {
            return implode(' ', $words);
        }

        $index = random_int(0, $count - 2);
        $tmp = $words[$index];
        $words[$index] = $words[$index + 1];
        $words[$index + 1] = $tmp;

        return implode(' ', $words);
    }

    private function misspell(string $line): string
    {
        if (preg_match('/[^\x00-\x7F]/', $line)) {
            return $line . ' ?';
        }

        $length = strlen($line);
        if ($length <= 1) {
            return $line . $line;
        }

        if ($length <= 3) {
            return $line . 's';
        }

        $chars = str_split($line);
        $index = random_int(0, $length - 2);

        if (ctype_alnum($chars[$index]) && ctype_alnum($chars[$index + 1])) {
            $tmp = $chars[$index];
            $chars[$index] = $chars[$index + 1];
            $chars[$index + 1] = $tmp;

            return implode('', $chars);
        }

        array_splice($chars, $index, 1);
        return implode('', $chars);
    }

    private function forceDifferent(string $line): string
    {
        if (preg_match('/[^\x00-\x7F]/', $line)) {
            return $line . ' ?';
        }

        $chars = str_split($line);
        $lastIndex = count($chars) - 1;

        if ($lastIndex < 0) {
            return $line;
        }

        $last = $chars[$lastIndex];
        if (ctype_alpha($last)) {
            $next = chr(((ord(strtolower($last)) - 97 + 1) % 26) + 97);
            $chars[$lastIndex] = ctype_upper($last) ? strtoupper($next) : $next;
            return implode('', $chars);
        }

        return $line . 'x';
    }
}
