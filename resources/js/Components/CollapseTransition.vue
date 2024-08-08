<script setup>
// import { nextTick } from "vue";
const props = defineProps({
    duration:{type:Number, default:250},
    easingEnter:{type:String, default:"ease-in-out"} ,
    easingLeave:{type:String, default:"ease-in-out"} ,
    opacityClosed: {type:Number, default:0},
    opacityOpened: {type:Number, default:1},
})

const closed = "0px";
function getElementStyle(element) {
  return {
    height: element.style.height,
    width: element.style.width,
    position: element.style.position,
    visibility: element.style.visibility,
    overflow: element.style.overflow,
    paddingTop: element.style.paddingTop,
    paddingBottom: element.style.paddingBottom,
    borderTopWidth: element.style.borderTopWidth,
    borderBottomWidth: element.style.borderBottomWidth,
    marginTop: element.style.marginTop,
    marginBottom: element.style.marginBottom,
  };
}

function prepareElement(element, initialStyle) {
  const { width } = getComputedStyle(element);
  element.style.width = width;
  element.style.position = "absolute";
  element.style.visibility = "hidden";
  element.style.height = "";

  let { height } = getComputedStyle(element);
  element.style.width = initialStyle.width;
  element.style.position = initialStyle.position;
  element.style.visibility = initialStyle.visibility;
  element.style.height = closed;
  element.style.overflow = "hidden";
  return initialStyle.height && initialStyle.height != closed
    ? initialStyle.height
    : height;
}

function animateTransition(
  element,
  initialStyle,
  done,
  keyframes,
  options
) {
  const animation = element.animate(keyframes, options);
  // Set height to 'auto' to restore it after animation
  element.style.height = initialStyle.height;
  animation.onfinish = () => {
    element.style.overflow = initialStyle.overflow;
    done();
  };
}

function getEnterKeyframes(height, initialStyle) {
  return [
    {
      height: closed,
      opacity: props.opacityClosed,
      paddingTop: closed,
      paddingBottom: closed,
      borderTopWidth: closed,
      borderBottomWidth: closed,
      marginTop: closed,
      marginBottom: closed,
    },
    {
      height,
      opacity: props.opacityOpened,
      paddingTop: initialStyle.paddingTop,
      paddingBottom: initialStyle.paddingBottom,
      borderTopWidth: initialStyle.borderTopWidth,
      borderBottomWidth: initialStyle.borderBottomWidth,
      marginTop: initialStyle.marginTop,
      marginBottom: initialStyle.marginBottom,
    },
  ];
}

function enterTransition(element, done) {
  const HTMLElement = element;
  const initialStyle = getElementStyle(HTMLElement);
  const height = prepareElement(HTMLElement, initialStyle);
  const keyframes = getEnterKeyframes(height, initialStyle);
  const options = { duration: props.duration, easing: props.easingEnter };
  animateTransition(HTMLElement, initialStyle, done, keyframes, options);
}

function leaveTransition(element, done) {
  const HTMLElement = element;
  const initialStyle = getElementStyle(HTMLElement);
  const { height } = getComputedStyle(HTMLElement);
  HTMLElement.style.height = height;
  HTMLElement.style.overflow = "hidden";

  const keyframes = getEnterKeyframes(height, initialStyle).reverse();
  const options = { duration: props.duration, easing: props.easingLeave };

  animateTransition(HTMLElement, initialStyle, done, keyframes, options);
}
</script>

<template>
  <Transition :css="false" @enter="enterTransition" @leave="leaveTransition">
    <slot />
  </Transition>
</template>