import * as YUKA from "yuka";
import * as THREE from "three";

class PathCreator {
    constructor(vehicle) {
        this.path = new YUKA.Path();

        this.currentPath = [];
        this.isDrawing = false;
        this.lines = null;

        this.path.add(new YUKA.Vector3(3, 0, -0.5));
        this.path.add(new YUKA.Vector3(7, 0, -0.5));
        this.path.add(new YUKA.Vector3(7, 0, 13));
        this.path.add(new YUKA.Vector3(2, 0, 40));
        // path.add(new YUKA.Vector3(0, 0, 0));
        // path.add(new YUKA.Vector3(4, 0, -4));
        // path.add(new YUKA.Vector3(6, 0, 0));
        // path.add(new YUKA.Vector3(4, 0, 4));
        // path.add(new YUKA.Vector3(0, 0, 6));

        this.path.loop = true;

        this.vehicleFollowPath(vehicle);
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

        if (this.path._index > 1) {
            console.log("yess");
            // vehicle.steering.add(followPathBehivor);
            vehicle.active = false;
        }
        this.tick = (delta) => {
            // console.log(this.path._index);
        };
    }
    vehicleFollowPath(vehicle) {
        vehicle.position.copy(this.path.current());
        const followPathBehivor = new YUKA.FollowPathBehavior(this.path, 0.5);
        vehicle.steering.add(followPathBehivor);
        const onPathBehavior = new YUKA.OnPathBehavior(this.path);
        vehicle.steering.add(onPathBehavior);
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
}

export { PathCreator };
