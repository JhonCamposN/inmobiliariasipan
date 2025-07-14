const video = document.getElementById('heroVideo');
let reverse = false;
let reverseInterval = null;

video.addEventListener('ended', () => {
    reverse = true;
    video.pause();
    reverseInterval = setInterval(() => {
        if (video.currentTime <= 0.05) {
            clearInterval(reverseInterval);
            reverse = false;
            video.currentTime = 0;
            video.play();
        } else {
            video.currentTime -= 0.04;
        }
    }, 40);
});

// Si el usuario interactúa, asegúrate de que el video siga funcionando correctamente
video.addEventListener('play', () => {
    if (reverse) {
        clearInterval(reverseInterval);
        reverse = false;