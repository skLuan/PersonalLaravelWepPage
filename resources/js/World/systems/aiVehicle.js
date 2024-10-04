import { Vehicle } from "yuka"

function aivehicle (mesh) {
    const vehicle = new Vehicle();
        vehicle.setRenderComponent(mesh, sync);
    return
}
function sync(entity, renderComponent) {
    renderComponent.matrix.copy(entity.worldMatrix);
}

export { aivehicle }