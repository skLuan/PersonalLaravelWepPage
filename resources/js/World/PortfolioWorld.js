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
import LoaderManager from "./systems/LoaderManager.js";
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

        scene.add(camera, hemisphereLight, helper);

        //------------------------- Yuka ------------
        //const path = new PathCreator();

        // -------------------------------- Obtener el elemento <main>
        const mainEl = document.querySelector("main");
        if (mainEl) {
            // Configura la cámara en una posición fija
            camera.position.set(0, 30, 0); // Escala la altura (ajusta el factor 0.01 según necesidad)
            camera.lookAt(0, 0, 0);
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
        loop.updatables.push(camera);

        const resizer = new Resizer(container, camera, renderer);
    }
    async init() {
        // -------------------------------- Meshes
        const squidManager = entityManager([]);
        const path = new PathCreator();

        console.log("Creating squids");
        const squidPromises = [];
        for (let i = 0; i < 5; i++) {
            squidPromises.push(createSquid(scene, "purple", 0.1, 0.5));
        }
        const squids = await Promise.all(squidPromises);
        const scale = 0.03;
        squids.forEach((squid) => {
            squid.scale.set(scale, scale, scale);
            squid.position.set(
            MathUtils.randFloatSpread(40),
            0,
            MathUtils.randFloatSpread(100)
            );
            squid.rotation.fromEuler(0, 2 * Math.PI * Math.random(), 0);
            squid.updatePath(path.getPaths());
            squidManager.add(squid);
        });
        scene.add(path.lines);
        loop.updatables.push(squidManager, path);
        this.start();
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

function getScene() {
    return scene;
}

export { PortfolioWorld, getScene };
