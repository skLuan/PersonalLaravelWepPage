import * as YUKA from "yuka";

function newVehicle(mesh) {
    const vehicle = new YUKA.Vehicle();
    vehicle.setRenderComponent(mesh, sync);

    function sync(entity, renderComponent) {
        renderComponent.matrix.copy(entity.worldMatrix);
    }

    return vehicle;
}

export { newVehicle };
