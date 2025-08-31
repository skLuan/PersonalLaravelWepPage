const menuBtn = document.querySelector('#burger-menu-btn');
const menuBtnIcon = menuBtn.querySelector('.icony');
const mobileMenu = document.querySelector('#mobile-menu');

const topBtn = document.querySelector('#top-btn');

function toggler() {
    mobileMenu.classList.toggle('translate-x-full');
    mobileMenu.classList.toggle('mx-1');

    menuBtn.classList.toggle('border-skl-pink');
    menuBtn.classList.toggle('border-skl-purple');

    menuBtnIcon.classList.toggle('text-skl-white-pink');
    menuBtnIcon.classList.toggle('text-skl-pink');
}

document.addEventListener('click',(e) =>{
    if (!menuBtn.contains(e.target) && !mobileMenu.contains(e.target) && menuBtn.classList.contains('border-skl-pink')) {
        console.log('yes');
        toggler();
    }
})
menuBtn.addEventListener('click',(e) => {
    toggler();
})

window.addEventListener('scroll', () => {
    if (window.scrollY >= window.innerHeight) {
        topBtn.parentElement.style.transform = 'translateX(0)';
        topBtn.style.pointerEvents = 'auto';
    } else {
        topBtn.parentElement.style.transform = 'translateX(60px)';
        topBtn.style.pointerEvents = 'none';
    }
});
topBtn.addEventListener('click',(e) => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
});
