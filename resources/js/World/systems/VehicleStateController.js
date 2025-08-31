import * as YUKA from "yuka";
import { PathCreator } from "../components/PathCreator";
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
        this.path.add(new YUKA.Vector3(-3, 0, 3));
        this.path.add(new YUKA.Vector3(3, 0, -7));
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
        this.alignment();
        //this.states.follow();
    }
    updateState(stateName) {
        //this.vehicle.steering.clear();
        const state = this.states[stateName];
        if (typeof state === "function") {
            this.follow();
        } else {
            console.warn(`State "${stateName}" does not exist.`);
        }
    }
    wander() {
        const wanderBehavior = new YUKA.WanderBehavior();
        wanderBehavior.weight = 1.2; // Ajustar el peso del comportamiento
        this.vehicle.steering.add(wanderBehavior);
    }
    alignment() {
        const alignmentBehavior = new YUKA.AlignmentBehavior();
        alignmentBehavior.weight = 1.5;

        const cohesionBehavior = new YUKA.CohesionBehavior();
        cohesionBehavior.weight = 0.2;
        const separationBehavior = new YUKA.SeparationBehavior();
        separationBehavior.weight = 5;
        this.vehicle.steering.add(separationBehavior);
        this.vehicle.steering.add(cohesionBehavior);
        // this.vehicle.steering.add(alignmentBehavior);
    }
    follow() {
        // Remove any existing FollowPathBehavior before adding a new one
        this.vehicle.steering.behaviors = this.vehicle.steering.behaviors.filter(
            (b) => !(b instanceof YUKA.FollowPathBehavior)
        );
        //this.vehicle.steering.clear();
        //this.vehicle.position.copy(this.path.current());
        const followPathBehavior = new YUKA.FollowPathBehavior(this.path, 3);
        this.vehicle.steering.add(followPathBehavior);
    }
    removeSteering(BehaviorType) {
        this.vehicle.steering.behaviors = this.vehicle.steering.behaviors.filter(
            (b) => !(b instanceof BehaviorType)
        );
    }
    onPath() {
        const onPathBehavior = new YUKA.OnPathBehavior(this.path);
        onPathBehavior.weight = 1; // Ajustar el peso del comportamiento
        this.vehicle.steering.add(onPathBehavior);
    }
    updatePath(newPath) {
        this.path = new YUKA.Path();
        newPath._waypoints.forEach((point) => {
            this.path.add(point.clone());
        });
        this.path.loop = true;
        this.follow(); // Reapply the follow behavior with the new path
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
