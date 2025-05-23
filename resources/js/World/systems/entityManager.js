import * as YUKA from "yuka";

function entityManager(vehicles) {
    const entityManager = new YUKA.EntityManager();
    const time = new YUKA.Time();

    vehicles.forEach((vehicle) => {
        entityManager.add(vehicle);
    });
    entityManager.tick = (delta) => {
        // add tick metod to push to loop
        const deltaYuka = time.update().getDelta();
        entityManager.update(deltaYuka);
    };

    return entityManager;
}

export { entityManager };
