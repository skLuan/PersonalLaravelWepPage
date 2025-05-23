import { createCamera } from "./components/camera.js";
import { createCube, createBasicCube } from "./components/cube.js";
import { createSphere } from "./components/sphere.js";
import { createScene } from "./components/scene.js";
import {
    createAmbientLight,
    createDirectionalLight,
    createPointLight,
} from "./components/lights.js";
import { createCone } from "./components/cone.js";
import { createControls } from "./systems/controls.js";
import { createRenderer } from "./systems/renderer.js";
import { helpers } from "./systems/helpers.js";
import { Resizer } from "./systems/Resizer.js";
import { Loop } from "./systems/Loop.js";
import { createGroup, createMeshGroup } from "./components/meshGroup.js";
import { MathUtils } from "three";
import * as YUKA from "yuka";
import * as THREE from "three";
import { getDomElementCoordsAsYuka } from "./systems/coordenatesSettler";
import { createSquid } from "./components/squid.js";
import { entityManager } from "./systems/entityManager.js";
import { PathCreator } from "./components/PathCreator.js";
//import { loadBirds } from "./components/birds/birds.js";

let camera;
let renderer;
let scene;
let light;
let loop;
let cubeGroup;
class PortfolioWorld {
    constructor(container) {
        // -------------------------------- Helpers
        const helper = helpers();

        camera = createCamera();
        scene = createScene();
        renderer = createRenderer();
        container.append(renderer.domElement);
        // -------------------------------- Lights
        light = createDirectionalLight(8);
        light.position.set(-10, 16, 0);
        camera.add(light);

        const { ambientLight, hemisphereLight } = createAmbientLight(8, 5);
        const pointLightOne = createPointLight();
        // -------------------------------- Loop Init
        loop = new Loop(camera, scene, renderer);
        // -------------------------------- Meshes
        const squid = createSquid(scene,"purple", 0.2, 1.3);
        
        scene.add(camera, hemisphereLight, helper);

        //------------------------- Yuka ------------
        const path = new PathCreator(squid);
        scene.add(path.lines);

        const squidManager = entityManager([squid]);


        // -------------------------------- Obtener el elemento <main>
        const mainEl = document.querySelector("main");
        if (mainEl) {
            // Configura la cámara en una posición fija
            camera.position.set(5, 30, 10); // Escala la altura (ajusta el factor 0.01 según necesidad)
            camera.lookAt(5, 0, 10);
            let lastScrollY = window.pageYOffset;
            document.addEventListener("scroll", () => {
                const currentScrollY = window.pageYOffset;
                const delta = currentScrollY - lastScrollY;
                camera.position.z += delta * 0.03; // Suma la distancia de scroll (negativo hacia abajo)
                lastScrollY = currentScrollY;
            });
        } else {
            console.warn(
                "Elemento <main> no encontrado, usando valor por defecto"
            );
            mainTopPosition = 500; // Valor por defecto
        }
        document.addEventListener("click", (event) => {
            const rect = renderer.domElement.getBoundingClientRect();
            const x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
            const y = -((event.clientY - rect.top) / rect.height) * 2 + 1;
            const vector = new THREE.Vector3(x, 0, y);
            vector.unproject(camera);
            console.log("World position:", vector);
        });

        loop.updatables.push(camera, squidManager,path);

        const resizer = new Resizer(container, camera, renderer);
    }

    async init() {
        //await loadBirds();
    }

    keyBoardKeys() {
        window.addEventListener("keypress", (e) => {
            // console.log(e.key);
            switch (e.key) {
                case " ":
                    console.log("espacio");
                    cubeGroup.visible = !cubeGroup.visible;
                    break;
                default:
                    break;
            }
        });
    }
    render() {
        renderer.render(scene, camera);
    }
    start() {
        this.keyBoardKeys();
        loop.start();
    }
    stop() {
        loop.stop();
    }
}

export { PortfolioWorld };
