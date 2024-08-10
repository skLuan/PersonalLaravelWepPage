import { WebGLRenderer } from "three";

function createRenderer() {
  const renderer = new WebGLRenderer({'antialias': true, alpha:true});
  return renderer;
}

export { createRenderer };