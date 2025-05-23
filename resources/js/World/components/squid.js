import { Mesh, Color, MeshBasicMaterial, ConeGeometry, MathUtils } from "three";
import { createMaterial, loadTexture } from "./material.js";
import { newVehicle } from "../systems/vehicleCreator.js";

function createSquid(scene,
    _color = "papayawhip",
    radius = 1,
    _height = 2,
    segments = 16
) {
    const material = createMaterial(_color);
    // const emissiveMap = loadTexture("/assets/textures/uv-test-bw.png");
    // material.lightMap = emissiveMap;
    const geometry = new ConeGeometry(radius, _height, segments, segments);
    geometry.rotateX(Math.PI * 0.5);
    const squid = new Mesh(geometry, material);
    squid.matrixAutoUpdate = false; // Disable automatic matrix updates for Yuka
    //console.log(squid);
    scene.add(squid);
    const vehicle = newVehicle(squid);

    vehicle.tick = (delta) => {
        // miniCube.position.x += 1*delta;
    };

    return vehicle;
}

export { createSquid };
