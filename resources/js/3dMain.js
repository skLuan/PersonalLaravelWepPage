import { PortfolioWorld } from "./World/PortfolioWorld";

export async function main() {
    const container = document.querySelector("#scene-container");
    const btnRender = document.querySelector("#btn-render");

    const portfolioWorld = new PortfolioWorld(container);
    // btnRender.addEventListener('click', (e)=>{
    //     // console.log('sisa');
    // });

    await portfolioWorld.init();
    portfolioWorld.start();
}

main().catch((err) => {
    console.error(err);
});
