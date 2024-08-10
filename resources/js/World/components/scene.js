import {
  Color,
  Scene,
} from "three";

function createScene(color = null){
const _color = color;

const scene = new Scene();
scene.background = _color !== null? new Color(_color) : _color;
return scene;
}
export {createScene}