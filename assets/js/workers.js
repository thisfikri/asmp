var workerURL = window.location.origin + '/' + window.location.pathname.split('/')[1] + '/assets/js/worker.notification-checker.js';

if (window.Worker) {
    console.log('Workers...');
    var worker = new Worker(workerURL);
    const audioElement = document.querySelector('audio');
    worker.onmessage = function(e) {
        var data = JSON.parse(e.data.result);
        if (data.newSMstatus == true && Number.parseInt($('.notification-area .mail-count-num b').text()) < data.newSMCount) {
            $('.mail-count-num').css('display', 'inline-block');
            $('.mail-count-num b').text(data.newSMCount);
            var n = new Notification('Surat Masuk', { icon: baseURL() + '/assets/images/logo/asmp-browser-icon.png', body: data.newSMCount + ' Surat Masuk Yang Belum Dibaca', lang: 'ind_ID', tag: '1' });
            n.addEventListener('show', function() {
                audioElement.play();
                setTimeout(n.close.bind(n), 4000);
            });
            n.addEventListener('click', function() {
                n.close();
                window.location.replace(baseURL(getAccess() + '/surat-masuk'));
            });
            if (data.fl_action != 0) {
                let aSecurity = new ASMPSecurity(),
                    gll, sll;
                gll = aSecurity.getLoggedLink();
                switch (gll.status) {
                    case 1:
                        let flpCode = window.prompt(gll.message);
                        sll = aSecurity.setLoggedLink(2);
                        if (sll.hasOwnProperty('status')) {
                            window.alert(sll.message);
                        }
                        break;
                    case 3:
                        window.alert(gll.message);

                        function fl_timeout() {
                            sll = aSecurity.setLoggedLink(4);
                            if (sll.status == 'success') {
                                window.location.replace(sll.message);
                            } else if (sll.status == 'error' || sll.status == 'failed') {
                                window.alert(sll.message);
                            }
                        }
                        aSecurity.setTimer(30, 1);
                        setTimeout(fl_timeout, 30000);
                        break;
                    default:
                        break;
                }
            }
        } else if (data.newSMstatus == false && data.newSMCount == 0) {
            audioElement.pause();
            $('.mail-count-num').css('display', 'none');
            $('.mail-count-num b').text(data.newSMCount);
        }
    }
    worker.postMessage(getAccess());
}