import * as YUKA from "yuka";
import * as THREE from "three";

class PathCreator {
    constructor() {
        this.path = new YUKA.Path();

        this.currentPath = [];
        this.isDrawing = false;
        this.lines = null;

        this.path.add(new YUKA.Vector3(-3, 0, -10));
        this.path.add(new YUKA.Vector3(3, 0, -10));
        this.path.add(new YUKA.Vector3(3, 0, 4));
        this.path.add(new YUKA.Vector3(-3, 0, 3));
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
        const lineMaterial = new THREE.LineBasicMaterial({ color: 0xffffff });
        this.lines = new THREE.LineLoop(lineGeometry, lineMaterial);

        // Agregar event listener para hacer la ruta responsiva
        window.addEventListener('resize', () => this.responsivePath());

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
