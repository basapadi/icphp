<script>
import { reactiveOmit } from "@vueuse/core";
import { Label as RekaLabel } from "reka-ui";
import { cn } from "@/lib/utils";

export default {
  name: "Label",
  components: { RekaLabel },
  props: {
    class: { type: [String, Array, Object], default: "" },
    // semua props dari LabelProps dari reka-ui diteruskan secara dinamis
  },
  setup(props) {
    // Hilangkan prop "class" agar tidak diteruskan ke RekaLabel
    const delegatedProps = reactiveOmit(props, ["class"]);

    // Gabungkan class bawaan dengan class dari props
    const labelClass = cn(
      "text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70",
      props.class
    );

    return { delegatedProps, labelClass };
  }
};
</script>

<template>
  <RekaLabel v-bind="delegatedProps" :class="labelClass">
    <slot />
  </RekaLabel>
</template>
