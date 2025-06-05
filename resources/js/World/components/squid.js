import { Mesh, Color, MeshBasicMaterial, ConeGeometry, MathUtils } from "three";
import { createMaterial, loadTexture } from "./material.js";
import { newVehicle } from "../systems/vehicleCreator.js";
import { GLTFLoader, RGBELoader } from "three/examples/jsm/Addons.js";
import LoaderManager from "../systems/LoaderManager.js";

async function createSquid(scene,
    _color = "papayawhip",
    radius = 1,
    _height = 2,
    segments = 16
) { 
    const loaderManager = LoaderManager.getManager();
    const gltfLoader = new GLTFLoader(loaderManager);
    const rgbeLoader = new RGBELoader();
    let squid = null;
    
    const gltfPromise = await gltfLoader.loadAsync("/3d_meshes/giant-squid/source/squid.gltf");
    squid = gltfPromise.scene.children[0];
    //console.log("Gltf loaded", squid)
    // const scale = 0.01;
    // squid.scale.set(scale, scale, scale);
    squid.rotateY(Math.PI * 0.5);
    squid.matrixAutoUpdate = false; // Disable automatic matrix updates for Yuka
    // const material = createMaterial(_color);
    // const emissiveMap = loadTexture("/assets/textures/uv-test-bw.png");
    // material.lightMap = emissiveMap;
    // const geometry = new ConeGeometry(radius, _height, segments, segments);
    // geometry.rotateX(Math.PI * 0.5);
    // squid = new Mesh(geometry, material);
    //console.log(squid);
    scene.add(squid);
    const vehicle = newVehicle(squid);

    vehicle.tick = (delta) => {
        // miniCube.position.x += 1*delta;
    };

    return vehicle;
}

export { createSquid };
