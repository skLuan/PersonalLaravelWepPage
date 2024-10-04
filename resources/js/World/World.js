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
import { createRenderer } from "./systems/renderer";
import { helpers } from "./systems/helpers.js";
import { Resizer } from "./systems/Resizer.js";
import { Loop } from "./systems/Loop.js";
import { createGroup, createMeshGroup } from "./components/meshGroup.js";
import { MathUtils } from "three";
import * as YUKA from "yuka";
import * as THREE from "three";
//import { loadBirds } from "./components/birds/birds.js";

let camera;
let renderer;
let scene;
let light;
let loop;
let cubeGroup;
class World {
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
        // -------------------------------- Controls
        const controls = createControls(camera, renderer.domElement);
        // -------------------------------- Meshes
        const cone = new createCone("purple", 0.2, 1.3);
        // cubeGroup = createGroup();
        // cubeGroup.add(cube);
        camera.position.y = 20;
        cone.matrixAutoUpdate = false;
        
        camera.rotateY(MathUtils.degToRad(90));
        loop.updatables.push(controls, cone);
        
        scene.add(camera, cone, hemisphereLight, helper);
        // scene.add(sphere);
        
        //------------------------- Yuka ------------
        const vehicle = new YUKA.Vehicle();
        vehicle.setRenderComponent(cone, sync);
        //vehicle.rotateTo (MathUtils.degToRad(90));

        function sync(entity, renderComponent) {
            renderComponent.matrix.copy(entity.worldMatrix);
        }

        const path = new YUKA.Path();
        path.add(new YUKA.Vector3(-4, 0, 4));
        path.add(new YUKA.Vector3(-6, 0, 0));
        path.add(new YUKA.Vector3(-4, 0, -4));
        path.add(new YUKA.Vector3(0, 0, 0));
        path.add(new YUKA.Vector3(4, 0, -4));
        path.add(new YUKA.Vector3(6, 0, 0));
        path.add(new YUKA.Vector3(4, 0, 4));
        path.add(new YUKA.Vector3(0, 0, 6));

        path.loop = true;

        vehicle.position.copy(path.current());
        const followPathBehivor = new YUKA.FollowPathBehavior(path, 0.5);
        vehicle.steering.add(followPathBehivor);
        const onPathBehavior = new YUKA.OnPathBehavior(path);
        vehicle.steering.add(onPathBehavior);

        const entityManager = new YUKA.EntityManager();
        entityManager.add(vehicle);

        const position = [];
        for (let i = 0; i < path._waypoints.length; i++) {
            const waypoint = path._waypoints[i];
            position.push(waypoint.x, waypoint.y,waypoint.z)
        }
        const lineGeometry = new THREE.BufferGeometry();
        lineGeometry.setAttribute('position', new THREE.Float32BufferAttribute(position,3))
        const lineMaterial = new THREE.LineBasicMaterial({color: 0xffffff})
        const lines = new THREE.LineLoop(lineGeometry, lineMaterial)
        scene.add(lines);

        const time = new YUKA.Time();

        entityManager.tick = (delta) => { // add tick metod to push to loop
          const deltaYuka = time.update().getDelta();
          entityManager.update(deltaYuka);
        }

        loop.updatables.push(entityManager);


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

export { World };
