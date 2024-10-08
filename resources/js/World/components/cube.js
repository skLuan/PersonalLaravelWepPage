import {
  Mesh,
  MeshMatcapMaterial,
  MeshStandardMaterial,
  BoxGeometry,
  MathUtils
} from "three";
import { createMaterial, loadTexture } from "./material.js";
const _mt = 1.5;

function miniCube() {
  // -------- mini cube
  const geometryMini = new BoxGeometry(0.4, 0.4, 0.4);
  const materialMini = createMaterial("pink");
  const texture = loadTexture("/assets/textures/uv-test-bw.png");
  materialMini.metalnessMap = texture;
  materialMini.metalness = 0.5;
  const miniCube = new Mesh(geometryMini, materialMini);

  miniCube.position.set(0, 2, 0);
  miniCube.rotation.set(MathUtils.degToRad(30), 0, -1);

  miniCube.tick = (delta) => {
    const rotation = gradosARadianes(360);
    miniCube.rotation.z -= rotation * delta;
    miniCube.rotation.x -= rotation * delta;
    miniCube.rotation.y -= rotation * delta;

    // miniCube.position.x += 1*delta;
  };
  return miniCube;
}

function createCube(
  _color = "cornsilk",
  texture = null
) {
  const material = createMaterial(_color, texture);
  const geometry = new BoxGeometry(_mt, _mt, _mt);
  const cube = new Mesh(geometry, material);

  cube.tick = (delta) => {
    const rotation = MathUtils.degToRad(3.6);
    cube.rotation.z += rotation * delta;
    cube.rotation.x += rotation * delta;
    cube.rotation.y += rotation * delta;
  };
  window.addEventListener("keydown", (e) => {
    switch (e.key) {
      case "l":
        break;

      default:
        break;
    }
  });

  cube.rotation.set(0, MathUtils.degToRad(45), 0);
  // cube.rotateZ(gradosARadianes(45));
  return cube;
}
function createDefaultCube(args = {}) {
  args.color === null || args.color === undefined ? (args.color = "white") : "";
  // args.texture === null || args.texture === undefined
  //   ? (args.texture = "")
  //   : "";
  args.mt === null || args.mt === undefined ? (args.mt = _mt) : "";
  args.width === null || args.width === undefined ? (args.width = _mt) : "";
  args.height === null || args.height === undefined ? (args.height = _mt) : "";
  args.depth === null || args.depth === undefined ? (args.depth = _mt) : "";

  const material = createMaterial(args.color, args.texture);
  const geometry = new BoxGeometry(args.width, args.height, args.depth);
  const cube = new Mesh(geometry, material);

  cube.tick = (delta) => {
  };
  window.addEventListener("keydown", (e) => {
    switch (e.key) {
      default:
        break;
    }
  });
  // cube.rotateZ(gradosARadianes(45));
  return cube;
}

function createBasicCube() {
  const material = new MeshMatcapMaterial();
  const geometry = new BoxGeometry(_mt, _mt, _mt);
  const cube = new Mesh(geometry, material);
  cube.rotateX(MathUtils.degToRad(45));
  cube.rotateY(MathUtils.degToRad(45));
  return cube;
}

export { createCube, createBasicCube, miniCube, createDefaultCube };
