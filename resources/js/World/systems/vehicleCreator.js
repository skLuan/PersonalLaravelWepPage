import { Object3D } from "three";
import * as YUKA from "yuka";
import {VehicleStateController} from "./VehicleStateController.js";
/**
 * Creates a new vehicle.
 * @param {Object3D} mesh - The mesh to associate with the vehicle.
 * @returns The created vehicle.
 */
function newVehicle(mesh) {
    const vehicle = new YUKA.Vehicle();
    vehicle.setRenderComponent(mesh, sync);

    vehicle.stateController = new VehicleStateController(vehicle); // Controlador de estado del vehículo
    function sync(entity, renderComponent) {
        renderComponent.matrix.copy(entity.worldMatrix);
    }
    vehicle.tick = function (delta) {
        vehicle.stateController.tick();
    }
    return vehicle;
}

export { newVehicle };
