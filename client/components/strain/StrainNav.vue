<template>
    <div class="sticky-top sticky strains__sticky">
        <ul class="nav justify-content-around justify-content-sm-center strains__nav">
            <li class="nav-item text-center">
                <vue-autosuggest
                    ref="autocomplete"
                    v-model="query"
                    :suggestions="suggestions"
                    :inputProps="inputProps"
                    :sectionConfigs="sectionConfigs"
                    :getSuggestionValue="getSuggestionValue"
                    @input="fetchResults"
                    @focus="hideFooter()"
                    @blur="showFooter()"
                >
                    <template slot-scope="{suggestion}">
                        <a :href="`/marijuana-strains/${suggestion.item.slug}`"><span class="my-suggestion-item">{{suggestion.item.name}}</span></a>
                    </template>
                </vue-autosuggest>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="">Indica</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="">Sativa</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="">Hybrid</a>
            </li>
        </ul>
    </div>
</template>

<script>
import { VueAutosuggest } from "vue-autosuggest";
import axios from "axios";

export default {
    name: "StrainNav",
    components: {
        VueAutosuggest
    },
    data() {
        return {
            query: "",
            results: [],
            timeout: null,
            // selected: null,
            debounceMilliseconds: 250,
            strainsUrl: "/marijuana-strains",
            inputProps: {
                id: "autosuggest__input",
                placeholder: "🔍 Search Strains",
                class: "strains__search",
                name: "hello"
            },
            suggestions: [],
            sectionConfigs: {
                destinations: {
                    limit: 6,
                    label: "Destination",
                    onSelected: selected => {
                        this.selected = selected.item;
                    }
                },
            },
            strains : [],
        };
    },
    mounted() {
        const strainsPromise = axios.get(this.strainsUrl);
        strainsPromise.then((res) => {
            this.strains = res.data;
            this.suggestions.push({ name: "strains", data: res.data });
        })
    },
    methods: {
        fetchResults() {
            const query = this.query;
            clearTimeout(this.timeout);
            this.timeout = setTimeout(() => {
                    this.suggestions = [];
                    const strains = this.filterResults(this.strains, query, "name");
                    strains.length && this.suggestions.push({ name: "strains", data: strains });
            }, this.debounceMilliseconds);
        },
        filterResults(data, text, field) {
            let result = data.filter( item => { return item[field].toLowerCase().includes(text.toLowerCase()); })
                                .sort(function(a, b){
                                    let x = a.name.toLowerCase();
                                    let y = b.name.toLowerCase();
                                    if (x < y) {return -1;}
                                    if (x > y) {return 1;}
                                    return 0;
                                });

            for(let i = result.length-1 ; i >= 0 ; i--) {
                let el = result[i];
                if(el[field].toLowerCase().indexOf(text.toLowerCase()) == 0) {
                    let index = result.findIndex(item => item.id === el.id);
                    result.splice(index, 1);
                    result.unshift(el);
                }
            }

            return result;
        },
        getSuggestionValue(suggestion) {
            let { name, item, slug } = suggestion;
            // this.$router.push({ name: 'strain_detail', params: {strain: item.slug} });
            window.open(`/marijuana-strains/${item.slug}`, '_self');

        },
        hideFooter() {
            if(window.is_mobile) {
                $("#footer_bar").hide();
            }
        },
        showFooter() {
            if(window.is_mobile) {
                $("#footer_bar").show();
            }
        }
    }
};
</script>

<style lang="css">

    #autosuggest__input {
        outline: none;
        position: relative;
        display: block;
        /* border: 1px solid #616161; */
        /* padding: 10px; */
        /* width: 100%; */
        box-sizing: border-box;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
    }

    #autosuggest__input.autosuggest__input-open {
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
    }

    .autosuggest__results-container {
        position: relative;
        width: 100%;
    }

    @media only screen and (max-width: 800px) {
        .autosuggest__results-container {
            width: 140%;
        }
    }

    .autosuggest__results {
        font-weight: 300;
        font-size: 16px;
        margin: 0;
        position: absolute;
        z-index: 10000001;
        width: 100%;
        border: 1px solid #706f6f;
        border-bottom-left-radius: 4px;
        border-bottom-right-radius: 4px;
        background: #000;
        /* padding: 1rem; */
        max-height: 350px;
        overflow-y: scroll;
        text-align: left;
    }

    .autosuggest__results ul {
        list-style: none;
        padding-left: 0;
        margin: 0;
    }

    .autosuggest__results .autosuggest__results-item {
        cursor: pointer;
        padding: 10px;
    }

    #autosuggest ul:nth-child(1) > .autosuggest__results_title {
        border-top: none;
    }

    .autosuggest__results .autosuggest__results-before {
        color: gray;
        font-size: 11px !important;
        margin-left: 0;
        /* padding: 15px 13px 5px; */
        /* border-top: 1px solid lightgray; */
    }

    .autosuggest__results .autosuggest__results-item:active,
    .autosuggest__results .autosuggest__results-item:hover,
    .autosuggest__results .autosuggest__results-item:focus,
    .autosuggest__results .autosuggest__results-item.autosuggest__results-item--highlighted {
        background-color: #f6f6f6;
    }
</style>
