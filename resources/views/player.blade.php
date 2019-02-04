<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $video['title']  }}</title>

    <!-- To use the outline effect, add this class: font-effect-outline -->
    <link href="https://fonts.googleapis.com/css?family=Questrial|Lato:300&effect=outline" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/css/player.css">

    <script>
        const video = {!! json_encode($video) !!}
    </script>
</head>
<body>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/hls.js@canary"></script>

<div class="player-container">
    <div id="player-overlay-gradient" class="player-overlay"></div>

    <div id="player-info" class="player-info">
        <div class="player-info-left">
            <p class="player-info-text-large" id="video-title"></p>

            <div class="player-info-subheader mt-1">
                <p class="player-info-text-small" id="video-year"></p>

                <div class="imdb ml-1 player-info-text-small">
                    <a target="_blank" rel="noopener" id="imdb-link">
                        <i id="imdb-logo" class="fa fa-imdb"></i>
                        <span style="margin-left: 4px; vertical-align: text-top;" id="imdb-rating"></span>
                    </a>
                </div>
            </div>

            <span class="player-horizontal-line ml-2"></span>
            <p id="time-left" class="mt-1 player-info-text-small"></p>
            <span class="player-horizontal-line ml-2"></span>

            <p id="summary" class="player-info-text-small mt-1 player-info-summary"></p>
        </div>
    </div>

    <video id="player" poster="{{ $video['poster'] }}" height="720px" controls>
    </video>
</div>

<script>
    const player = document.getElementById('player');

    if (Hls.isSupported()) {
        const hlsConfig = {
            autoStartLoad: true,
            startPosition: -1,
            capLevelToPlayerSize: false,
            debug: true,
            defaultAudioCodec: undefined,
            initialLiveManifestSize: 1,
            maxBufferLength: 30,
            maxMaxBufferLength: 600,
            maxBufferSize: 250 * 1000 * 1000,
            maxBufferHole: 0.5,
            lowBufferWatchdogPeriod: 0.5,
            highBufferWatchdogPeriod: 3,
            nudgeOffset: 0.1,
            nudgeMaxRetry: 3,
            maxFragLookUpTolerance: 0.2,
            enableWorker: true,
            enableSoftwareAES: true,
            startLevel: -1,
            levelLoadingTimeOut: 10000,
            levelLoadingMaxRetry: 4,
            levelLoadingRetryDelay: 500,
            levelLoadingMaxRetryTimeout: 64000,
            fragLoadingTimeOut: 20000,
            fragLoadingMaxRetry: 6,
            fragLoadingRetryDelay: 500,
            fragLoadingMaxRetryTimeout: 64000,
            startFragPrefetch: true,
            appendErrorMaxRetry: 3,
            enableWebVTT: true,
            enableCEA708Captions: true,
            stretchShortVideoTrack: true,
            maxAudioFramesDrift: 500,
            forceKeyFrameOnDiscontinuity: true,
            abrEwmaFastVoD: 4.0,
            abrEwmaSlowVoD: 15.0,
            abrEwmaDefaultEstimate: 500000,
            abrBandWidthFactor: 0.95,
            abrBandWidthUpFactor: 0.7,
        };

        const hls = new Hls(hlsConfig);
        hls.loadSource("{{ $stream }}");
        hls.attachMedia(player);

        hls.on(Hls.Events.MANIFEST_PARSED, function (event, data) {
            console.log('manifest loaded, found ' + data.levels.length + ' quality level');

            player.play();
        });

        hls.on(Hls.Events.ERROR, function (event, data) {
            console.error(event, data);

            if (data.fatal) {
                switch (data.type) {
                    case Hls.ErrorTypes.NETWORK_ERROR:
                        // try to recover network error
                        console.error('fatal network error encountered, try to recover');
                        hls.startLoad();
                        break;
                    case Hls.ErrorTypes.MEDIA_ERROR:
                        console.error('fatal media error encountered, try to recover');
                        hls.recoverMediaError();
                        break;
                    default:
                        // cannot recover
                        console.error({event, data});
                        hls.destroy();
                        break;
                }
            }
        });
    }
    // hls.js is not supported on platforms that do not have Media Source Extensions (MSE) enabled.
    // When the browser has built-in HLS support (check using `canPlayType`), we can provide an HLS manifest (i.e. .m3u8 URL) directly to the video element throught the `src` property.
    // This is using the built-in support of the plain video element, without using hls.js.
    // Note: it would be more normal to wait on the 'canplay' event below however on Safari (where you are most likely to find built-in HLS support) the video.src URL must be on the user-driven
    // white-list before a 'canplay' event will be emitted; the last video event that can be reliably listened-for when the URL is not on the white-list is 'loadedmetadata'.
    else if (player.canPlayType('application/vnd.apple.mpegurl')) {
        player.src = "{{ $stream }}";
        player.addEventListener('loadedmetadata', function () {
            player.play();
        });
    }

    const playerInfo = document.getElementById('player-info');

    const overlayElements = [];

    const title = document.getElementById('video-title');
    title.innerText = video.title;

    const year = document.getElementById('video-year');
    year.innerText = `Released: ${video.year}`;

    const poster = document.createElement('img');
    poster.src = video.poster;
    poster.classList.add('player-overlay-poster');
    playerInfo.appendChild(poster);

    const summary = document.getElementById('summary');
    summary.innerText = video.summary;

    const imdbLink = document.getElementById('imdb-link');
    imdbLink.href = `https://www.imdb.com/title/${video.imdbId}`;

    const imdbRating = document.getElementById('imdb-rating');
    imdbRating.innerText = video.imdbRating;

    const overlayGradient = document.getElementById('player-overlay-gradient');
    overlayElements.push(overlayGradient);

    const overlay = document.getElementById('player-info');
    overlayElements.push(overlay);

    player.addEventListener('playing', () => {
        overlayElements.forEach(el => el.classList.add('player-hidden'));
    });

    player.addEventListener('pause', () => {
        overlayElements.forEach(el => el.classList.remove('player-hidden'));
    });

    const timeLeft = document.getElementById('time-left');
    player.addEventListener('timeupdate', () => {
        const videoTimeLeft = moment.duration(player.duration - player.currentTime, 'seconds').humanize();

        timeLeft.innerText = `Time left: ${videoTimeLeft}`;
    });
</script>

</body>
</html>
