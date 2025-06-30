<script setup>
import { Link } from "@inertiajs/vue3";
import axios from "axios";
import { ref } from "vue";
// This component displays a payment success message and allows the user to download an invoice.
// It uses Inertia.js for navigation and Axios for making HTTP requests.
defineProps({
    message: String,
    transaction:Object
})

const downloading = ref(false);

 const downloadFile = async (reference) => {
    downloading.value = true;
   try {
   const res = await axios.get(`/api/invoices/${reference}`, {
    responseType: 'blob'
   })
   const data = await res.data;
   const blob = new Blob([data]);
   console.log(data)
   console.log(blob);
   const url = window.URL.createObjectURL(blob);
   const link = document.createElement('a');
   link.href = url;
   link.setAttribute('download', `invoice-${reference}.pdf`);
   document.body.appendChild(link);
   link.click();
   document.body.removeChild(link);
   window.URL.revokeObjectURL(url);
    downloading.value = false;
   }catch (err){
    console.log(err.message)
   }
 }

</script>
 <template>
    <div class="flex flex-col items-center justify-center h-screen bg-gray-100">
     <div class="text-3xl font-bold text-center mt-10">
        Payment Success
     </div>
     <div>{{ message }}</div>
     <div><span class="font-bold">Reference:</span> {{ transaction.reference }}</div>
     <div><span class="font-bold">Amount:</span> {{ transaction.amount/100 }}</div>
     <div><span class="font-bold">Email:</span> {{ transaction.email }}</div>
    <Link
       :href="route('dashboard')"
       class="mt-6 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
    >
       Go to Dashboard
    </Link>
    <button
      @click="downloadFile(transaction.reference)"
      class="mt-6 px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 transition"
      :disabled="downloading"
    >
      <span v-if="downloading">
        Downloading...
        <svg
         class="animate-spin h-5 w-5 ml-2 inline-block"
         xmlns="http://www.w3.org/2000/svg"
         fill="none"
         viewBox="0 0 24 24"
        >
         <circle
           class="opacity-25"
           cx="12"
           cy="12"
           r="10"
           stroke="currentColor"
           stroke-width="4"
         ></circle>
         <path
           class="opacity-75"
           fill="currentColor"
           d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
         ></path>
        </svg>
      </span>
      <span v-else> Download Invoice</span>
    </button>

    </div>
</template>
