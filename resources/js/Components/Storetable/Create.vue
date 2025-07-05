<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
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

const form = useForm({
    storeid: '',
    name: '',
    cutofftime: '16:00',
    types: '16:01',
});

const submitForm = () => {
    form.post("/store", {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

const emit = defineEmits();

const toggleActive = () => {
    emit('toggleActive');
};

</script>

<template>
    <Modal title="CREATE NEW STORE" @toggle-active="toggleActive" :show-modal="showModal">
        <template #content >
            <FormComponent @submit.prevent="submitForm"  >
                
                <div class="grid grid-cols-1">
                    <div class="col-span-1">
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <InputLabel for="storeid" value="STOREID" />
                                <TextInput
                                    id="storeid"
                                    v-model="form.storeid"
                                    type="text"
                                    class="mt-1 block w-full"
                                    :is-error="form.errors.storeid ? true : false"
                                    autofocus
                                />
                                <InputError :message="form.errors.storeid" class="mt-2" />
                            </div>
                            
                            <div>
                                <InputLabel for="name" value="STORENAME" />
                                <TextInput
                                    id="name"
                                    v-model="form.name"
                                    :is-error="form.errors.name ? true : false"
                                    type="text"
                                    class="mt-1 block w-full"
                                />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>
                            
                            <div>
                                <InputLabel for="cutofftime" value="CUTOFF TIME" />
                                <TextInput
                                    id="cutofftime"
                                    v-model="form.cutofftime"
                                    :is-error="form.errors.cutofftime ? true : false"
                                    type="time"
                                    class="mt-1 block w-full"
                                />
                                <InputError :message="form.errors.cutofftime" class="mt-2" />
                            </div>
                            
                            <div>
                                <InputLabel for="types" value="TYPES" />
                                <SelectOption 
                                    id="types"
                                    v-model="form.types" 
                                    :is-error="form.errors.types ? true : false"
                                    class="mt-1 block w-full !bg-gray-100"
                                >
                                    <option disabled value="">Select an option</option>
                                    <option>NONE</option>
                                    <option>MOQ</option>
                                </SelectOption>
                                <InputError :message="form.errors.types" class="mt-2" />
                            </div>
                        </div>
                    </div>
                </div>
                
            </FormComponent>
        </template>
        <template #buttons>
            <PrimaryButton type="submit" @click="submitForm" :disabled="form.processing" :class="{ 'opacity-25': form.processing }">
                SUBMIT
            </PrimaryButton>
        </template>
    </Modal>
</template>