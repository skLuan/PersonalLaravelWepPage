import { createCamera } from "./components/camera.js";
import { createCube, createBasicCube } from "./components/cube.js";
import { createSphere } from "./components/sphere.js";
import { createScene } from "./components/scene.js";
import {
  createAmbientLight,
  createDirectionalLight,
  createPointLight,
} from "./components/lights.js";

import { createControls } from "./systems/controls.js";
import { createRenderer } from "./systems/renderer";
import { helpers } from "./systems/helpers.js";
import { Resizer } from "./systems/Resizer.js";
import { Loop } from "./systems/Loop.js";
import { createGroup, createMeshGroup } from "./components/meshGroup.js";
import { MathUtils } from "three";
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
    light.position.set(-10,16,0);
    camera.add(light);

    const {ambientLight, hemisphereLight } = createAmbientLight(8, 5);
    const pointLightOne = createPointLight();
    // -------------------------------- Loop Init    
    loop = new Loop(camera, scene,renderer);
    // -------------------------------- Controls    
    const controls = createControls(camera, renderer.domElement);
    // -------------------------------- Meshes    
    const cube = new createCube("purple");
    // cubeGroup = createGroup();
    // cubeGroup.add(cube);
    camera.position.y = 20;
    camera.rotateY(MathUtils.degToRad(90));
    loop.updatables.push(controls, cube);
    scene.add(camera, cube, hemisphereLight, helper);
    // scene.add(sphere);

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
  stop(){
    loop.stop();
  }
}

export { World };