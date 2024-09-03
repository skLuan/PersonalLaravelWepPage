import {
  Mesh,
  Color,
  MeshBasicMaterial,
  ConeGeometry,
  MathUtils,
} from "three";
import { createMaterial, loadTexture } from "./material.js";

function createCone(_color = "papayawhip", radius = 1, segments = 16, _height = 2) {
  const material = createMaterial(_color);
  // const emissiveMap = loadTexture("/assets/textures/uv-test-bw.png");
  // material.lightMap = emissiveMap;
  const geometry = new ConeGeometry(radius, _height, segments, segments);
  const cone = new Mesh(geometry, material);
  cone.tick = (delta) => {
    // miniCube.position.x += 1*delta;
  };
  return cone;
}

export { createCone };
