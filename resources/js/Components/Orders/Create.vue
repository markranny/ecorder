<script setup>
import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import Modal from "@/Components/Modals/DaisyModal.vue";
import PrimaryButton from '@/Components/Buttons/PrimaryButton.vue';
import InputLabel from '@/Components/Inputs/InputLabel.vue';
import TextInput from '@/Components/Inputs/TextInput.vue';
import SelectOption from '@/Components/SelectOption/SelectOption.vue';
import InputError from '@/Components/Inputs/InputError.vue';
import FormComponent from '@/Components/Form/FormComponent.vue';

const props = defineProps({
    showModal: {
        type: Boolean,
        default: false,
    }
});

const initialStoreId = computed(() => {
    return $page.props.auth.user.storeid;
});

const form = useForm({
    storeid: '',
    description: '',
    orderDate: '',
});

const dateError = ref('');
const isDateTaken = ref(false);

// Watch for changes in the date input
watch(() => form.orderDate, async (newDate) => {
    if (newDate) {
        try {
            const response = await axios.get(`/check-order-date/${newDate}/${$page.props.auth.user.storeid}`);
            isDateTaken.value = response.data.exists;
            dateError.value = isDateTaken.value ? 'Order already exists for this date' : '';
        } catch (error) {
            console.error('Error checking date:', error);
        }
    }
});

const submitForm = () => {
    if (isDateTaken.value) {
        return;
    }
    
    form.post("/order", {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            isDateTaken.value = false;
            dateError.value = '';
        },
    });
};

const emit = defineEmits();

const toggleActive = () => {
    emit('toggleActive');
};
</script>

<template>
    <Modal title="CREATE NEW ORDER" @toggle-active="toggleActive" :show-modal="showModal">
        <template #content>
            <FormComponent @submit.prevent="submitForm">
                <div class="grid grid-cols-1">
                    <div class="col-span-1">
                        <div class="grid grid-cols-1 gap-4">
                            <!-- Store ID field -->
                            <div>
                                <InputLabel for="STORE" value="Your order has been automatically generated with transfer request #" />
                                <TextInput
                                    id="storeid"
                                    v-model="form.storeid"
                                    type="text"
                                    class="mt-1 block w-full hidden"
                                    :is-error="form.errors.storeid ? true : false"
                                    :value="$page.props.auth.user.storeid"
                                    autofocus
                                    disabled
                                />
                                <InputError :message="form.errors.storeid" class="mt-2" />
                            </div>
                            
                            <!-- Date Selection field with real-time validation -->
                            <div>
                                <InputLabel for="orderDate" value="Select Order Date" />
                                <TextInput
                                    id="orderDate"
                                    v-model="form.orderDate"
                                    type="date"
                                    class="mt-1 block w-full border rounded-md focus:ring focus:ring-opacity-50 bg-white text-black"
                                    :class="[
                                        isDateTaken ? 'border-red-500' : 'border-gray-300',
                                        'focus:ring-blue-500 focus:border-blue-500',
                                    ]"
                                    :is-error="isDateTaken || form.errors.orderDate ? true : false"
                                    required
                                />

                                <div v-if="dateError" class="mt-2 text-sm text-red-600">{{ dateError }}</div>
                                <InputError :message="form.errors.orderDate" class="mt-2" />
                            </div>
                        </div>
                    </div>
                </div>
            </FormComponent>
        </template>
        <template #buttons>
            <PrimaryButton 
                type="submit" 
                @click="submitForm" 
                :disabled="form.processing || isDateTaken" 
                :class="{ 'opacity-25': form.processing || isDateTaken }"
            >
                Generate
            </PrimaryButton>
        </template>
    </Modal>
</template>

<style>
/* General Styles */
input[type="date"] {
    color-scheme: light !important; /* Force light mode for the date picker */
    background-color: white !important;
    color: black !important;
}

/* Light Mode */
:root input[type="date"] {
    background-color: white !important;
    color: black !important;
}

/* Even in Dark Mode, keep the input white */
@media (prefers-color-scheme: dark) {
    input[type="date"] {
        background-color: white !important; 
        color: black !important;
    }
}
</style>