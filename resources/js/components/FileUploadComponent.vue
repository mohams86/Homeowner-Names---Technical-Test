<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">Laravel Vue JS File Upload</div>
          <div class="card-body">
            <div v-if="success != ''" class="alert alert-success">
              {{ success }}
            </div>
            <form @submit="formSubmit" enctype="multipart/form-data">
              <input
                type="file"
                accept=".csv"
                class="form-control mb-3"
                v-on:change="onChange"
              />
              <button class="btn btn-primary mb-3">Upload</button>
            </form>
            <div v-if="finalperson_arr != ''">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th class="text-center">Title</th>
                    <th class="text-center">First Name</th>
                    <th class="text-center">Initial</th>
                    <th class="text-center">Last Name</th>
                  </tr>
                </thead>
                <tr v-for="persondata in finalperson_arr">
                  <td class="text-center">{{ persondata.title }}</td>
                  <td class="text-center">{{ persondata.first_name }}</td>
                  <td class="text-center">{{ persondata.initial }}</td>
                  <td class="text-center">{{ persondata.last_name }}</td>
                </tr>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import axios from "axios";
export default {
  data() {
    return {
      name: "",
      file: "",
      success: "",
      finalperson_arr: "",
    };
  },
  methods: {
    onChange(e) {
      this.file = e.target.files[0];
    },
    formSubmit(e) {
      if (!this.file) {
        e.preventDefault();
        alert("No file chosen");
        return;
      }
      e.preventDefault();
      let existingObj = this;
      const config = {
        headers: {
          "content-type": "multipart/form-data",
        },
      };
      let data = new FormData();
      data.append("file", this.file);
      axios
        .post("/upload", data, config)
        .then(function (res) {
          existingObj.success = res.data.success;
          existingObj.finalperson_arr = res.data.finalperson_arr;
        })
        .catch(function (err) {
          existingObj.output = err;
        });
    },
  },
};
</script>
