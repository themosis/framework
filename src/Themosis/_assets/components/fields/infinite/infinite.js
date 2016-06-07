import $ from 'jquery';
import _ from 'underscore';
import InfiniteView from './InfiniteView';
import './infinite.styl';

// Implementation.
// List all infinite fields.
let infinites = $('div.themosis-infinite-container');

_.each(infinites, elem =>
{
   let infinite = $(elem),
       infiniteViewElem = infinite.find('table.themosis-infinite>tbody').first(),
       rows = infiniteViewElem.children('tr.themosis-infinite-row');

   // Create an infiniteView instance for each infinite field.
   new InfiniteView({
       el: infiniteViewElem,
       rows: rows
   });
});