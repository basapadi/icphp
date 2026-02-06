<script>
import { reactiveOmit } from "@vueuse/core";
import { Label as RekaLabel } from "reka-ui";
import { cn } from "@/lib/utils";

export default {
    name: "Label",
    components: { RekaLabel },
    props: {
        class: { type: [String, Array, Object], default: "" },
    },
    setup(props) {
        const delegatedProps = reactiveOmit(props, ["class"]);

        const labelClass = cn(
            "text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70",
            props.class
        );

        return { delegatedProps, labelClass };
    },
};
</script>

<template>
    <RekaLabel v-bind="delegatedProps" :class="labelClass">
        <slot />
    </RekaLabel>
</template>
