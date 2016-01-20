import $ from 'jquery';
import _ from 'underscore';
import InfiniteView from './InfiniteView';
import './infinite.styl';

// Implementation.
// List all infinite fields.
let infinites = $('div.themosis-infinite-container').closest('tr');

_.each(infinites, elem =>
{
   let infinite = $(elem),
       rows = infinite.find('tr.themosis-infinite-row');

   // Create an infiniteView instance for each infinite field.
   new InfiniteView({
       el: infinite.find('table.themosis-infinite>tbody'),
       rows: rows
   });
});