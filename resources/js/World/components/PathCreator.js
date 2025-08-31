import * as YUKA from "yuka";
import * as THREE from "three";

/**
 * PathCreator is a utility class for creating and managing a YUKA.Path and its corresponding THREE.js line visualization.
 * It supports responsive path updates and can convert DOM elements to path waypoints.
 *
 * @class
 * @example
 * const pathCreator = new PathCreator(0xff0000);
 * pathCreator.addToScene(scene);
 *
 * @param {number|string} [_color=0xffffff] - The color of the path line (THREE.Color format).
 * @param {YUKA.Path} [_path=null] - Optional. An existing YUKA.Path instance to use. If not provided, a default path is created.
 *
 * @property {YUKA.Path} path - The underlying YUKA.Path instance.
 * @property {THREE.LineLoop} lines - The THREE.js line object representing the path.
 * @property {boolean} isDrawing - Indicates if the path is currently being drawn.
 * @property {Array} currentPath - Stores the current path points during drawing.
 *
 * @method addToScene
 * @param {THREE.Scene} scene - The THREE.js scene to add the path line to.
 *
 * @method DOMtoPath
 * @param {string} querySelector - CSS selector for DOM elements to convert to path waypoints.
 *
 * @method toWorldCoords
 * @param {number} x - The x coordinate in screen space.
 * @param {number} y - The y coordinate in screen space.
 * @returns {Object} An object with x, y, z properties representing world coordinates.
 *
 * @method getPaths
 * @returns {YUKA.Path} The current YUKA.Path instance.
 *
 * @method responsivePath
 * Updates the path waypoints and geometry based on window size for responsive design.
 */
class PathCreator {

    constructor(_color = "0xffffff", _path = null) {
        this.currentPath = [];
        this.isDrawing = false;
        this.lines = null;
        if (_path) {
            this.path = _path;
        } else {
            this.path = new YUKA.Path();
            this.path.add(new YUKA.Vector3(-3, 0, -38));
            this.path.add(new YUKA.Vector3(17, 0, -38));
            this.path.add(new YUKA.Vector3(17, 0, 7));
            this.path.add(new YUKA.Vector3(-15, 0, 5));
        }
        //this.path.add(new YUKA.Vector3(2, 0, 40));
        // path.add(new YUKA.Vector3(0, 0, 0));
        // path.add(new YUKA.Vector3(4, 0, -4));
        // path.add(new YUKA.Vector3(6, 0, 0));
        // path.add(new YUKA.Vector3(4, 0, 4));
        // path.add(new YUKA.Vector3(0, 0, 6));

        this.path.loop = true;

        const position = [];
        for (let i = 0; i < this.path._waypoints.length; i++) {
            const waypoint = this.path._waypoints[i];
            position.push(waypoint.x, waypoint.y, waypoint.z);
        }
        const lineGeometry = new THREE.BufferGeometry();
        lineGeometry.setAttribute(
            "position",
            new THREE.Float32BufferAttribute(position, 3)
        );
        const lineMaterial = new THREE.LineBasicMaterial({ color: _color });
        this.lines = new THREE.LineLoop(lineGeometry, lineMaterial);

        // Agregar event listener para hacer la ruta responsiva
        window.addEventListener("resize", () => this.responsivePath());

        this.tick = (delta) => {
            // console.log(this.path._index);
        };
    }

    addToScene(scene) {
        scene.add(this.lines);
    }

    DOMtoPath(querySelector) {
        const toPath = document.querySelectorAll(querySelector);
        toPath.forEach((id) => {
            const vec = getDomElementCoordsAsYuka(id, this.toWorldCoords);
            if (vec) this.path.add(vec);
        });
    }

    // Supón que tienes una función que convierte coords de pantalla a mundo
    toWorldCoords(x, y) {
        // Implementa según tu lógica de cámara/proyección
        // Por ejemplo, usando THREE.js: unproject, etc.
        let newX = x * 0.01;
        let newY = y * 0.01;
        console.log(x, y);
        return { x: newX, y: 0, z: newY }; // Ejemplo simple
    }

    getPaths() {
        return this.path;
    }

    responsivePath() {
        // Implementa la lógica para hacer la ruta responsiva
        // Por ejemplo, actualizando los puntos de la ruta según el tamaño de la ventana
        if (window.innerWidth > 500) {
            // Limpiar los waypoints actuales
            this.path.clear();

            // Agregar waypoints para desktop
            this.path.add(new YUKA.Vector3(1, 0, -0.5));
            this.path.add(new YUKA.Vector3(7, 0, -0.5));
            this.path.add(new YUKA.Vector3(7, 0, 13));
            this.path.add(new YUKA.Vector3(2, 0, 40));
            this.path.loop = true;

            // Actualizar la geometría de la línea
            const position = [];
            for (let i = 0; i < this.path._waypoints.length; i++) {
                const waypoint = this.path._waypoints[i];
                position.push(waypoint.x, waypoint.y, waypoint.z);
            }
            this.lines.geometry.setAttribute(
                "position",
                new THREE.Float32BufferAttribute(position, 3)
            );
            this.lines.geometry.attributes.position.needsUpdate = true;
        }
    }
}

export { PathCreator };
