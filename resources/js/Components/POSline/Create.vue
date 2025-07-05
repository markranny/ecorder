<script setup>
import { ref, defineProps, defineEmits, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { watch, onMounted } from 'vue';
import Modal from "@/Components/Modals/Modal.vue";
import PrimaryButton from '@/Components/Buttons/PrimaryButton.vue';
import InputLabel from '@/Components/Inputs/InputLabel.vue';
import TextInput from '@/Components/Inputs/TextInput.vue';
import InputError from '@/Components/Inputs/InputError.vue';

const props = defineProps({
    showModal: {
        type: Boolean,
        default: false,
    },
    ITEMS: {
        type: Array,
        required: true,
    },
    OFFERID: {
        type: String,
        default: '',
    },
    DISCOUNTTYPE: {
        type: [String, Number],
        required: true,
    },
});

const form = useForm({
    OFFERID: '',
    LINEID: '',
    PRODUCTTYPE: '',
    ID: '',
    DEALPRICEORDISCPCT: '',
    LINEGROUP: '',
    DISCTYPE: '',
    NOOFLINESTOTRIGGER: '',
    DEALPRICEVALUE: '',
    DISCOUNTPCTVALUE: '',
    DISCOUNTAMOUNTVALUE: '',
    PRICEGROUP: '',
});

const selectedItemId = ref('');

const submitForm = () => {
    form.post("/posperiodicdiscountlines", {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

const emit = defineEmits();

const toggleActive = () => {
    emit('toggleActive');
};


onMounted(() => {
    form.OFFERID = props.OFFERID;

    form.DISCOUNTTYPE = props.DISCOUNTTYPE;

    form.ITEMS = props.ITEMS;
    
    watch(() => props.OFFERID, (newValue) => {
        form.OFFERID = newValue;
    });

    watch(() => props.DISCOUNTTYPE, (newValue) => {
        form.DISCOUNTTYPE = newValue;
    });

    watch(() => props.ITEMS, (newValue) => {
        form.ITEMS = newValue;
    });
});



</script>

<template>
    <Modal title="SELECT ITEM" @toggle-active="toggleActive" :show-modal="showModal">
        <template #content >
            <form @submit.prevent="submitForm"   class="px-2 py-3 max-h-[50vh] lg:max-h-[70vh] overflow-y-auto">
                <div class="grid grid-cols-3 ">

                    <div v-for="offerid in offerid" :key="offerid.offerid">
                        
                    </div>

                    <div class="col-span-3 ">
                    <InputLabel for="OFFERID" value="OFFERID" />
                    <TextInput
                        id="OFFERID"
                        v-model="form.OFFERID"
                        type="text"
                        class="mt-1 block w-full input"
                        :is-error="form.errors.OFFERID ? true : false"
                        autofocus
                        disabled
                    />
                    <InputError :message="form.errors.OFFERID" class="mt-2" />
                    </div>  

                    <!-- <div class="col-span-2">
                    <InputLabel for="LINEID" value="LINEID" />
                    <TextInput
                        id="LINEID"
                        v-model="form.LINEID"
                        :is-error="form.errors.LINEID ? true : false"
                        type="text"
                        class="mt-1 block w-full"
                    />
                    <InputError :message="form.errors.LINEID" class="mt-2" />
                    </div> -->

                    <!-- <div class="col-span-2">
                        <InputLabel for="PRODUCTTYPE" value="PRODUCTTYPE" />
                        <TextInput
                        id="PRODUCTTYPE"
                        v-model="form.PRODUCTTYPE"
                        :is-error="form.errors.PRODUCTTYPE ? true : false"
                        type="number"
                        class="mt-1 block w-full"
                        />
                        <InputError :message="form.errors.PRODUCTTYPE" class="mt-2" />
                    </div> -->

                    <!-- <div class="col-span-2 mt-4">
                    <InputLabel for="ID" value="ID" />
                    <TextInput
                        id="ID"
                        v-model="form.ID"
                        :is-error="form.errors.ID ? true : false"
                        type="number"
                        class="mt-1 block w-full"
                    />
                    <InputError :message="form.errors.ID" class="mt-2" />
                    </div> -->

                    <div class="col-span-2 mt-4">
                    <InputLabel for="item-select" value="Select an Item" />
                    <select
                        id="item-select"
                        v-model="selectedItemId"
                        class="mt-1 block w-full"
                    >
                        <option value="">-- Select an item --</option>
                        <option
                        v-for="item in props.items"
                        :key="item.itemid"
                        :value="item.itemid"
                        >
                        {{ item.itemname }}
                        </option>
                    </select>
                    </div>

                    <div class="col-span-3 ">
                    <InputLabel for="items" value="items" />
                    <TextInput
                        id="items"
                        v-model="form.items"
                        type="text"
                        class="mt-1 block w-full input"
                        autofocus
                        disabled
                    />
                    </div> 

                    <div class="col-span-1 ml-4 mt-4">
                        <InputLabel for="DISCTYPE" value="DISCTYPE" v-if="form.DISCOUNTTYPE === 'None'"/>
                        <select 
                            className="select select-bordered w-full max-w-full"
                            v-if="form.DISCOUNTTYPE === 'None'"
                            id="DISCTYPE"
                            name="DISCTYPE"
                            v-model="form.DISCTYPE"
                            :is-error="form.errors.DISCTYPE ? true : false"
                            autofocus
                            >
                            <option disabled selected>DISCTYPE</option>
                            <option value="0">None</option>
                            <option value="1">Deal Price</option>
                            <option value="2">Discount Percent</option>
                        </select>
                        <InputError :message="form.errors.DISCOUNTTYPE" class="mt-2" />

                    </div>

                    <div class="col-span-2 mt-4">
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

                    <!-- <div class="col-span-1 ml-4 mt-4">
                        <InputLabel for="DISCTYPE" value="DISCTYPE" />
                    <TextInput
                        id="DISCTYPE"
                        v-model="form.DISCTYPE"
                        :is-error="form.errors.DISCTYPE ? true : false"
                        type="number"
                        class="mt-1 block w-full"
                    />
                    <InputError :message="form.errors.DISCTYPE" class="mt-2" />
                    </div> -->

                    <div class="col-span-1 ml-4 mt-4">
                        <InputLabel for="DEALPRICE/DISCPCT" value="DEALPRICE/DISCPCT" />
                    <TextInput
                        id="DEALPRICEORDISCPCT"
                        v-model="form.DEALPRICEORDISCPCT"
                        :is-error="form.errors.DEALPRICEORDISCPCT ? true : false"
                        type="number"
                        class="mt-1 block w-full"
                    />
                    <InputError :message="form.errors.DEALPRICEORDISCPCT" class="mt-2" />
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
