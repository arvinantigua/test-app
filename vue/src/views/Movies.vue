<template>
	<div class="px1">
		<div v-if="loading" class="mt12 mxa" style="width:135px;">
			<w-spinner lg/>
		</div>
		<div v-else>
			<div class="my4">
				<div class="d-iblock" style="width:270px;">
					<w-input
					  outline
					  inner-icon-left="wi-search"
					  v-model="search">
					</w-input>
				</div>
				<div class="d-iblock">
					<w-button
					  class="ml1"
					  bg-color="primary"
					  color="success-light2"
					  @click="searchMovie">
					  Search
					</w-button>
				</div>
			</div>
			<w-grid columns="5" gap="1" class="wrapper mt5">
			  <div v-for="movie in movieData" class="box">
			  	<w-image :src="movie.Poster"></w-image>
			  	<div class="title4">{{ movie.Title }}</div>
			  	<div class="title5">{{ movie.Year }}</div>
			  	<w-rating v-model="movie.yourRate"  @input="rateMovie($event, movie.imdbID, movie.Title)"></w-rating>
			  	<br>
			  	<w-button
					  class="mt1"
					  bg-color="primary"
					  color="success-light2"
					  @click="sendCode">
					  Download
					</w-button>
			  </div>
			</w-grid>
			<br>
			<br>
			<hr>
			<div class="mxa" style="width:185px;">
				<w-button class="ma4" @click="previousPage" :disabled="disablePrevious">Previous</w-button>
				<w-button class="ma4" @click="nextPage" :disabled="disableNext">Next</w-button>
			</div>
      <w-dialog
        v-model="dialog.show"
        title="Download Code Sent"
        persistent
        :width="300">
        Please check your email and input the code.
        <div>
          <w-input v-model="dlCode">Code</w-input>
        </div>
        <i style="font-size: 14px;">
          All emails are sent to <a href="http://localhost:8100/" target="_blank">http://localhost:8100</a>. Open the link using a browser to view emails.
        </i>
        <br>
        <w-button v-if="errMsg" class="mt2" bg-color="error">
          {{ errMsg }}
        </w-button>

        <template #actions>
          <div class="spacer" />
          <w-button
            class="mr2"
            @click="dialog.show = false"
            bg-color="error"
            :disabled="downloading">
            Cancel
          </w-button>
          <w-button
            @click="downloadMovie"
            bg-color="success"
            :loading="downloading">
            Download
          </w-button>
        </template>
      </w-dialog>
		</div>
    <w-overlay
      v-model="showOverlay"
      persistent
      opacity="0.3">
      <div class="mt12 mxa" style="width:135px;">
        <w-spinner lg/>
      </div>
    </w-overlay>
	</div>
</template>

<script>
import OmdbService from "../services/omdb.service";

export default {
  name: "Movies",
  data() {
    return {
      movies: [],
      loading: false,
      search: null,
      dialog: { show: false },
      dlCode: null,
      showOverlay: false,
      errMsg: null,
      downloading: false,
    };
  },
  computed: {
    movieData() {
    	if (typeof this.movies.data != "undefined") {
      		return this.movies.data;
    	} else {
    		return [];
    	}
    },
    currentPage() {
    	if (typeof this.movies.currentPage != "undefined") {
      		return this.movies.currentPage;
    	} else {
    		return 0;
    	}
    },
    lastPage() {
    	if (typeof this.movies.lastPage != "undefined") {
      		return this.movies.lastPage;
    	} else {
    		return false;
    	}
    },
    disablePrevious() {
    	if (this.movieData.length && this.currentPage > 1) {
      		return false;
    	} else {
    		return true;
    	}
    },
    disableNext() {
    	if (this.movieData.length && this.currentPage < this.lastPage) {
      		return false;
    	} else {
    		return true;
    	}
    },
  },
  mounted() {
  	this.getData();
  },
  methods: {
    rateMovie($event, id, title) {
      OmdbService.getRateMovie($event, id).then(
	      () => {
	        this.$waveui.notify(`Rating for ${title} successful`, 'success')
	      },
	      (error) => {
	        console.log('error rating');
	      }
	  	);
    },
    sendCode() {
      this.showOverlay = true;
      OmdbService.sendCode().then((response) => {
        this.dlCode = null;
        this.dialog.show = true;
        this.showOverlay = false;
      },
      (error) => {
          this.showOverlay = false;
          console.log('error send code');
      });
    },
    downloadMovie() {
      this.errMsg = null;
      this.downloading = true;
      OmdbService.downloadMovie(this.dlCode).then((response) => {
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', 'sample.mp4');
        document.body.appendChild(link);
        link.click();
        this.downloading = false;
      },
      (error) => {
        this.downloading = false;
        this.errMsg = "Invalid Code";
      });
    },
    previousPage() {
      this.getData((this.currentPage - 1));
    },
    nextPage() {
    	this.getData((this.currentPage + 1));
    },
    searchMovie() {
    	this.getData(1);
    },
    getData(page = null) {
    	this.loading = true;
	    OmdbService.getData(this.search, page).then(
	      (response) => {
	        this.movies = response.data;
	        this.loading = false;
	      },
	      (error) => {
	      	this.loading = false;
	        console.log('error');
	      }
	    );
    },
  },
};
</script>
