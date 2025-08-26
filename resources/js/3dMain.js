import { PortfolioWorld } from "./World/PortfolioWorld";

export async function main() {
    const container = document.querySelector("#scene-container");
    if (!container) {
        console.error('Element with id "scene-container" not found.');
        return;
    }

    const portfolioWorld = new PortfolioWorld(container);

    await portfolioWorld.init();
    portfolioWorld.start();
}

main().catch((err) => {
    console.error(err);
});
