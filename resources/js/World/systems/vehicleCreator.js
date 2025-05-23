import { Object3D } from "three";
import * as YUKA from "yuka";
/**
 * Creates a new vehicle.
 * @param {Object3D} mesh - The mesh to associate with the vehicle.
 * @returns The created vehicle.
 */
function newVehicle(mesh) {
    const vehicle = new YUKA.Vehicle();
    vehicle.setRenderComponent(mesh, sync);

    function sync(entity, renderComponent) {
        renderComponent.matrix.copy(entity.worldMatrix);
    }

    return vehicle;
}

export { newVehicle };
