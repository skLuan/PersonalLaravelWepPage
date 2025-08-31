import * as YUKA from "yuka";

/**
 * Obtiene las coordenadas absolutas de un elemento DOM y las convierte a coordenadas YUKA.
 * @param {Node} elementId - El ID del elemento DOM.
 * @param {function} toWorldCoords - Función para convertir coords de pantalla a mundo.
 * @returns {YUKA.Vector3|null}
 */
export function getDomElementCoordsAsYuka(elementId, toWorldCoords) {
    const el = elementId;
    
    if (!el) return null;
    
    const rect = el.getBoundingClientRect();
    // Coordenadas absolutas respecto al documento
    const x = rect.left + window.scrollX + rect.width;
    const y = rect.top + window.scrollY + rect.height;
    
    // console.log(x, y);
    // Convierte a coordenadas del mundo 3D (debes definir esta función según tu escena)
    const worldCoords = toWorldCoords(x, y);
    if (!worldCoords) return null;

    // Retorna como YUKA.Vector3
    return new YUKA.Vector3(worldCoords.x, worldCoords.y, worldCoords.z);
}
