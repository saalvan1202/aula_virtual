<template>
    <div>
      <BFormCheckbox v-model="checked" name="check-button" switch>
        Switch Checkbox <b>(Checked: {{ checked }})</b>
      </BFormCheckbox>
    </div>
  </template>
  
  <script>
import { BFormCheckbox } from 'bootstrap-vue';

    export default {
      data() {
        return {
          checked: false
        }
      }
    }
  </script>  };
    },
};
</script>
