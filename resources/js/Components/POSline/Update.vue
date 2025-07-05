<script setup>
import { useForm } from '@inertiajs/vue3';
import { watch, onMounted } from 'vue';
import Modal from "@/Components/Modals/Modal.vue";
import PrimaryButton from '@/Components/Buttons/PrimaryButton.vue';
import InputLabel from '@/Components/Inputs/InputLabel.vue';
import TextInput from '@/Components/Inputs/TextInput.vue';
import InputError from '@/Components/Inputs/InputError.vue';

const props = defineProps({
    OFFERID:{
        type: [String, Number],
        required: true,
    },
    LINEID: {
        type: [String, Number],
        required: true,
    },
    PRODUCTTYPE: {
        type: [String, Number],
        required: true,
    },
    ID: {
        type: [String, Number],
        required: true,
    },
    DEALPRICEORDISCPCT: {
        type: [String, Number],
        required: true,
    },
    LINEGROUP: {
        type: [String, Number],
        required: true,
    },
    DISCTYPE: {
        type: [String, Number],
        required: true,
    },
    showModal: {
        type: Boolean,
        default: false,
    }
});

const form = useForm({
    OFFERID: '',
    LINEID: '',
    PRODUCTTYPE: '',
    ID: '',
    DEALPRICEORDISCPCT: '',
    LINEGROUP: '',
    DISCTYPE: '',
});
const submitForm = () => {
    form.patch("/posperiodicdiscountlines/patch", {
        preserveScroll: true,
    });
};

const emit = defineEmits();

const toggleActive = () => {
    emit('toggleActive');
};

onMounted(() => {
    form.OFFERID = props.OFFERID;
    form.LINEID = props.LINEID;
    form.PRODUCTTYPE = props.PRODUCTTYPE;
    form.ID = props.ID;
    form.DEALPRICEORDISCPCT = props.DEALPRICEORDISCPCT;
    form.LINEGROUP = props.LINEGROUP;
    form.DISCTYPE = props.DISCTYPE;
    
    watch(() => props.OFFERID, (newValue) => {
        form.OFFERID = newValue;
    });
    watch(() => props.LINEID, (newValue) => {
        form.LINEID = newValue;
    });
    watch(() => props.PRODUCTTYPE, (newValue) => {
        form.PRODUCTTYPE = newValue;
    });
    watch(() => props.ID, (newValue) => {
        form.ID = newValue;
    });
    watch(() => props.DEALPRICEORDISCPCT, (newValue) => {
        form.DEALPRICEORDISCPCT = newValue;
    });
    watch(() => props.LINEGROUP, (newValue) => {
        form.LINEGROUP = newValue;
    });
    watch(() => props.DISCTYPE, (newValue) => {
        form.DISCTYPE = newValue;
    });
});
</script>

<template>
    <Modal title="Posdiscount" @toggle-active="toggleActive" :show-modal="showModal">
        <template #content >
            <form @submit.prevent="submitForm"   class="px-2 py-3 max-h-[50vh] lg:max-h-[70vh] overflow-y-auto">
                <div class="grid grid-cols-3 ">

                    <div class="col-span-2 ">
                    <InputLabel for="OFFERID" value="OFFERID" />
                    <TextInput
                        id="OFFERID"
                        v-model="form.OFFERID"
                        type="text"
                        class="mt-1 block w-full"
                        :is-error="form.errors.OFFERID ? true : false"
                        autofocus
                    />
                    <InputError :message="form.errors.OFFERID" class="mt-2" />
                    </div>  

                    <div class="col-span-2">
                    <InputLabel for="LINEID" value="LINEID" />
                    <TextInput
                        id="LINEID"
                        v-model="form.LINEID"
                        :is-error="form.errors.LINEID ? true : false"
                        type="text"
                        class="mt-1 block w-full"
                    />
                    <InputError :message="form.errors.LINEID" class="mt-2" />
                    </div>

                    <div class="col-span-2">
                        <InputLabel for="PRODUCTTYPE" value="PRODUCTTYPE" />
                        <TextInput
                        id="PRODUCTTYPE"
                        v-model="form.PRODUCTTYPE"
                        :is-error="form.errors.PRODUCTTYPE ? true : false"
                        type="number"
                        class="mt-1 block w-full"
                        />
                        <InputError :message="form.errors.PRODUCTTYPE" class="mt-2" />
                    </div>

                    <div class="col-span-1 ml-4">
                    <InputLabel for="ID" value="ID" />
                    <TextInput
                        id="ID"
                        v-model="form.ID"
                        :is-error="form.errors.ID ? true : false"
                        type="number"
                        class="mt-1 block w-full"
                    />
                    <InputError :message="form.errors.ID" class="mt-2" />
                    </div>

                    <div class="col-span-1">
                        <InputLabel for="DEALPRICEORDISCPCT" value="DEALPRICEORDISCPCT" />
                    <TextInput
                        id="DEALPRICEORDISCPCT"
                        v-model="form.DEALPRICEORDISCPCT"
                        :is-error="form.errors.DEALPRICEORDISCPCT ? true : false"
                        type="number"
                        class="mt-1 block w-full"
                    />
                    <InputError :message="form.errors.DEALPRICEORDISCPCT" class="mt-2" />
                    </div>

                    <div class="col-span-2 ml-4">
                    <InputLabel for="LINEGROUP" value="LINEGROUP" />
                    <TextInput
                        id="LINEGROUP"
                        v-model="form.LINEGROUP"
                        type="text"
                        class="mt-1 block w-full"
                        :is-error="form.errors.LINEGROUP ? true : false"
                        autofocus
                    />
                    <InputError :message="form.errors.LINEGROUP" class="mt-2" />
                    </div>

                    <div class="col-span-1">
                        <InputLabel for="DISCTYPE" value="DISCTYPE" />
                    <TextInput
                        id="DISCTYPE"
                        v-model="form.DISCTYPE"
                        :is-error="form.errors.DISCTYPE ? true : false"
                        type="number"
                        class="mt-1 block w-full"
                    />
                    <InputError :message="form.errors.DISCTYPE" class="mt-2" />
                    </div>
                    
                </div>
            </form>
        </template>
        <template #buttons>
            <PrimaryButton type="submit" @click="submitForm" :disabled="form.processing" :class="{ 'opacity-25': form.processing }">
                SUBMIT
            </PrimaryButton>
        </template>
    </Modal>
</template>