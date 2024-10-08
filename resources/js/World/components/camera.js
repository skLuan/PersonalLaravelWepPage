import { PerspectiveCamera } from "three";

function createCamera(isGoing = false) {
  const camera = new PerspectiveCamera(
    40, // fov = Field Of View
    1, // aspect ratio (dummy value)
    0.1, // near clipping plane
    100 // far clipping plane
  );
  let isback = false;
  camera.tick = (delta) => {
    const move = 1 * delta;

    if (isGoing) {
      camera.position.z += move;
    } else if(isback) {
      camera.position.z -= move;
    }
  };

  window.addEventListener("keypress", (e) => {
    switch (e.key) {
      case "s" || "S":
        ("sisaaa");
        isGoing = !isGoing;
        isback? isback = false : '';
        break;
        case "w" || "W":
          isback = !isback;
          isGoing ? isGoing = false : "";
        
      break;
      default:
        break;
    }
    // if (e.key == "s" || e.key == "S") {

    // }
  });

  return camera;
}

export { createCamera };
