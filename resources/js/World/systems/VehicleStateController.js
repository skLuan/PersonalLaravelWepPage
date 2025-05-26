import * as YUKA from "yuka";
const target = new YUKA.Vector3(10, 0, 10); // Destino fijo
let hasSwitched = false;
const scrollThreshold = 500;

class VehicleStateController {
    vehicle;
    path;
    states;
    hasSwitched = false; // Definir como propiedad de la clase
    scrollThreshold = 500; // Umbral de scroll en píxeles
    target = new YUKA.Vector3(10, 0, 10); // Definir el destino
    /**
     * @param {YUKA.Vehicle} vehicle - The vehicle to control.
     */
    constructor(vehicle) {
        this.vehicle = vehicle;
        //---------------------
        this.path = new YUKA.Path();
        this.path.add(new YUKA.Vector3(3, 0, -0.5));
        this.path.add(new YUKA.Vector3(7, 0, -0.5));
        this.path.loop = true;
        //-------------------

        // Definir los estados como métodos vinculados
        this.states = {
            wander: this.wander.bind(this), // Vincular el contexto
            arrive: new YUKA.ArriveBehavior(this.target), // Usar el target definido
            follow: this.follow.bind(this), // Vincular el contexto
            onPath: this.onPath.bind(this), // Corregir nombre y vincular
        };
        //this.vehicle.steering.add(this.states.wander);
        window.addEventListener("scroll", () => {
            if (!hasSwitched && window.scrollY > scrollThreshold) {
                this.vehicle.steering.clear();
                this.vehicle.steering.add(this.states.wander);
                hasSwitched = true;
            }
        });
        this.states.wander();
    }
    wander() {
        console.log("Wander behavior activated");
        const wanderBehavior = new YUKA.WanderBehavior();
        this.vehicle.steering.add(wanderBehavior);
    }
    follow() {
        this.vehicle.position.copy(this.path.current());
        const followPathBehavior = new YUKA.FollowPathBehavior(this.path, 0.5);
        this.vehicle.steering.add(followPathBehavior);
    }
    onPath() {
        const onPathBehavior = new YUKA.OnPathBehavior(this.path);
        this.vehicle.steering.add(onPathBehavior);
    }
    updatePath(newPath) {
        this.path = new YUKA.Path();
        newPath._waypoints.forEach((point) => {
            this.path.add(point.clone());
        });
    }

    tick() {
        if (hasSwitched) {
            const distance = this.vehicle.position.distanceTo(target);
            if (distance < 0.5) {
                this.vehicle.steering.clear();
                const wanderBehavior = new YUKA.WanderBehavior();
                this.vehicle.steering.add(wanderBehavior);
                hasSwitched = false; // Reset to allow switching back
            }
        }
    }
}

export { VehicleStateController };
