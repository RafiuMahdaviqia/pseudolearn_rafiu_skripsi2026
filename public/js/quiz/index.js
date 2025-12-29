var target = document.querySelector("#quiz-container");
var blockUI = new KTBlockUI(target);

$(() => {
    blockUI.block();
    initClock();

    $('#sidebarToggle').click(function () {
        $('#quiz-container').toggleClass('container-fluid container');
    });
    blockUI.release();
});

initClock = () => {
    var timer = setInterval(function () {
        var date = new Date();
        var hours = date.getHours();
        var minutes = date.getMinutes();
        var seconds = date.getSeconds();
        var ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12;
        hours = hours < 10 ? '0' + hours : hours;
        minutes = minutes < 10 ? '0' + minutes : minutes;
        seconds = seconds < 10 ? '0' + seconds : seconds;
        var strTime = hours + ':' + minutes + ':' + seconds + ' ' + ampm;
        $('#clock').text(strTime);
    }, 1000);
}

