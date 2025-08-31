import { PortfolioWorld } from "./World/PortfolioWorld";
import { BlogWorld } from "./World/BlogWorld";
export async function main() {
    const container = document.querySelector("#scene-container");
    if (!container) {
        console.error('Element with id "scene-container" not found.');
        return;
    }
    const path = window.location.pathname.toLowerCase();
    let world;
    if (path.includes("/portfolio")) {
        console.log("Creating Portfolio World");
        world = new PortfolioWorld(container);
    } else if (path.includes("/blog")) {
        console.log("Creating Blog World");
        world = new BlogWorld(container);
    } else {
        console.log("Creating default Portfolio World");
        world = new PortfolioWorld(container);
    }
    await world.init();
    world.start();
}

main().catch((err) => {
    console.error(err);
});
