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
//import { loadBirds } from "./components/birds/birds.js";

let camera;
let renderer;
let scene;
let light;
let loop;
let cubeGroup;
let YukaPath;
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

        const path = new YUKA.Path();
        YukaPath = path;
        path.add(new YUKA.Vector3(3, 0, -0.5));
        path.add(new YUKA.Vector3(7, 0, 13));
        path.add(new YUKA.Vector3(2, 0, 40));
        // path.add(new YUKA.Vector3(0, 0, 0));
        // path.add(new YUKA.Vector3(4, 0, -4));
        // path.add(new YUKA.Vector3(6, 0, 0));
        // path.add(new YUKA.Vector3(4, 0, 4));
        // path.add(new YUKA.Vector3(0, 0, 6));

        // Supón que tienes una función que convierte coords de pantalla a mundo
        function toWorldCoords(x, y) {
            // Implementa según tu lógica de cámara/proyección
            // Por ejemplo, usando THREE.js: unproject, etc.
            let newX = x * 0.01;
            let newY = y * 0.01;
            console.log(x, y);
            return { x: newX, y: 0, z: newY }; // Ejemplo simple
        }

        const toPath = document.querySelectorAll(".to-path");
        // const path = new YUKA.Path();
        // toPath.forEach((id) => {
        //     const vec = getDomElementCoordsAsYuka(id, toWorldCoords);
        //     if (vec) path.add(vec);
        // });

        // document.addEventListener("DOMContentLoaded", () => {
        // });


        path.loop = true;

        squid.position.copy(path.current());
        const followPathBehivor = new YUKA.FollowPathBehavior(path, 0.5);
        squid.steering.add(followPathBehivor);
        const onPathBehavior = new YUKA.OnPathBehavior(path);
        squid.steering.add(onPathBehavior);

        const squidManager = entityManager([squid]);

        const position = [];
        for (let i = 0; i < path._waypoints.length; i++) {
            const waypoint = path._waypoints[i];
            position.push(waypoint.x, waypoint.y, waypoint.z);
        }
        const lineGeometry = new THREE.BufferGeometry();
        lineGeometry.setAttribute(
            "position",
            new THREE.Float32BufferAttribute(position, 3)
        );
        const lineMaterial = new THREE.LineBasicMaterial({ color: 0xffffff });
        const lines = new THREE.LineLoop(lineGeometry, lineMaterial);
        scene.add(lines);

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

        loop.updatables.push(camera, squidManager);

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
