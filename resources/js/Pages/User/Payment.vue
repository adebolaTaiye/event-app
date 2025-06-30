<script setup>
import Paystack from '@paystack/inline-js'
import axios from 'axios'
import { ref } from 'vue'
const props = defineProps({
  access_code: String,
});

const popup = new Paystack()

if(props.access_code === undefined ){
    //
    console.log('yyyyy')
}else {
    popup.resumeTransaction(props.access_code)
}

</script>
 <template>
     <div>

     </div>
 </template>
