import * as THREE from 'three';

let instance = null;

class LoaderManagerSingleton {
    constructor() {
        if (!instance) {
            this.manager = new THREE.LoadingManager();
            instance = this;
        }
        return instance;
    }

    getManager() {
        return this.manager;
    }
}

const LoaderManager = new LoaderManagerSingleton();

export default LoaderManager;