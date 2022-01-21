<template>
    <div class="container">
        <h1>Доски</h1>
        <form @submit.prevent="addNewDesk">
            <div class="mb-3">
                <input type="text" v-model="name" class="form-control" :class="{ 'is-invalid': $v.name.$error }" placeholder="Введите название доски">
                <div class="invalid-feedback" v-if="!$v.name.required">
                    Обязательное поле.
                </div>
                <div class="invalid-feedback" v-if="!$v.name.maxLength">
                    Максимальное количество символов: {{ $v.name.$params.maxLength.max }} .
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Добавить</button>
        </form>
        <div class="alert alert-danger mt-3" role="alert" v-if="errored">
            Ошибка загрузки данных. <br>
            {{ errors[0]}}
        </div>
        <div class="row">
            <div class="col-lg-4" v-for="desk in desks">
                <div class="card mt-3">
                    <router-link class="card-body" :to="{name: 'showDesk', params: {deskId: desk.id}}">
                        <h3 class="card-title">{{ desk.name }}</h3>
                    </router-link>
                    <button type="button" class="btn btn-danger mt-3" @click="deleteDesk(desk.id)">Удалить</button>
                </div>
            </div>
        </div>

        <div class="spinner-border text-primary" role="status" v-if="loading">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</template>

<script>
import { required, maxLength} from 'vuelidate/lib/validators'
export default {
    data() {
        return {
            desks: [],
            errored: false,
            errors: [],
            loading: true,
            name: null
        }
    },
    methods: {
        getAllDesks() {
            axios.get('/api/v1/desks')
                .then(response => {
                    this.desks = response.data.data
                })
                .catch(error => {
                    console.log(error)
                    this.errored = true
                })
                .finally(() => {
                    this.loading = false
                })
        },
        deleteDesk(id) {
            if(confirm('Вы действительно хотите удалить эту доску?')) {
                axios.post('/api/v1/desks/' + id, {
                    _method: 'DELETE'
                })
                    .then(response => {
                        this.desks = []
                        this.getAllDesks()
                    })
                    .catch(error => {
                        console.log(error)
                        this.errored = true
                    })
                    .finally(() => {
                        this.loading = false
                    })
            }
        },
        addNewDesk() {
            this.$v.name.$touch()
            if(this.$v.name.$anyError) {
                return;
            }
            axios.post('/api/v1/desks', {
                name: this.name,
            })
                .then(response => {
                    this.name = ''
                    this.desks = []
                    this.getAllDesks()
                })
                .catch(error => {
                    console.log(error.response)
                    if(error.response.data.errors.name) {
                        this.errors = []
                        this.errors.push(error.response.data.errors.name[0])
                    }
                    this.errored = true
                })
                .finally(() => {
                    this.loading = false
                })
        }
    },
    mounted() {
        this.getAllDesks()
    },
    validations: {
        name: {
            required,
            maxLength: maxLength(15)
        }
    }
}
</script>

