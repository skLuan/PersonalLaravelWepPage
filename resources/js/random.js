window.addEventListener('load', function () {
    // Todo tu código aquí
    initializePlayButton();
});
window.addEventListener('DOMContentLoaded', function () {
    // Crear el div absoluto, centrado, redondo, con fondo blanco
    const playButton = document.getElementById('playGlitchButton');
    const playDiv = document.createElement('div');

    const videoContainer = document.getElementById('principalVideo');

    playDiv.classList.add('play-button');
    playButton.appendChild(playDiv);
    playButton.classList.add('hidden');
    videoContainer.classList.add('hidden');
});

function initializePlayButton() {
    // Tomar el botón del documento
    const playButton = document.getElementById('playGlitchButton');
    const videoContainer = document.getElementById('principalVideo');
    const video = videoContainer.querySelector('video');
    if (video) {
        video.addEventListener('ended', function () {
            playButton.classList.remove('hidden'); // Mostrar el botón
        });
        video.addEventListener('play', function () {
            videoContainer.classList.remove('hidden');
            playButton.classList.add('hidden'); // Mostrar el botón
        });
        video.addEventListener('pause', function () {
            playButton.classList.remove('hidden'); // Mostrar el botón
        });
    } else {
        console.error('Video no encontrado');
    }
    playButton.classList.remove('hidden');
}
