import './bootstrap';
import { World } from './World/World';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
async function main() {
    const container = document.querySelector("#scene-container");
    const btnRender = document.querySelector('#btn-render');

    const world = new World(container);
    // btnRender.addEventListener('click', (e)=>{
    //     // console.log('sisa');
    // });

    await world.init();
    world.start();
}

main().catch((err) => {
    console.error(err);
});

Alpine.start();
