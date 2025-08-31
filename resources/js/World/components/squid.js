import { Mesh, Color, MeshBasicMaterial, ConeGeometry, MathUtils } from "three";
import { createMaterial, loadTexture } from "./material.js";
import { newVehicle } from "../systems/vehicleCreator.js";
import { GLTFLoader, RGBELoader } from "three/examples/jsm/Addons.js";
import LoaderManager from "../systems/LoaderManager.js";

async function createSquid(
    scene,
    _color = "papayawhip",
    radius = 1,
    _height = 2,
    segments = 16
) {
    const loaderManager = LoaderManager.getManager();
    const gltfLoader = new GLTFLoader(loaderManager);
    const rgbeLoader = new RGBELoader();
    let squid = null;
    //---- Squid .glb
    // const gltfPromise = await gltfLoader.loadAsync(
    //     "/3d_meshes/giant-squid/source/squid_mini2.glb"
    // );
    // squid = gltfPromise.scene;
    // squid.matrixAutoUpdate = false; // Disable automatic matrix updates for Yuka

    // console.log("Gltf loaded", squid)
    // ------------------------------- cone
    const scale = 40;
    const material = createMaterial(_color);
    const geometry = new ConeGeometry(radius, _height, segments, segments);
    geometry.rotateX(Math.PI * 0.5);
    squid = new Mesh(geometry, material);
    squid.scale.set(scale, scale, scale);
    squid.rotateY(Math.PI * 0.5);
    squid.matrixAutoUpdate = false; // Disable automatic matrix updates for Yuka
    const vehicle = newVehicle(squid);
    vehicle.rotation.fromEuler(MathUtils.degToRad(90), 0, 0);
    console.log("Gltf loaded", squid);
    //------------------------------------------------------------------------

    scene.add(vehicle._renderComponent);

    vehicle.tick = (delta) => {
        // miniCube.position.x += 1*delta;
    };

    return vehicle;
}

export { createSquid };
