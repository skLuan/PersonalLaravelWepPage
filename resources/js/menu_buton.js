const menuBtn = document.querySelector('#burger-menu-btn');
const menuBtnIcon = menuBtn.querySelector('.icony');
const mobileMenu = document.querySelector('#mobile-menu');

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
    console.log('sisaaaa')//toggler();
})

