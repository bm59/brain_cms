<style>
<!--
/* For modern browsers */
.parent:before,
.parent:after {
   content: "";
   display: table;
}
.parent:after {
   clear: both;
}

/* для IE6-7 */
.parent {
   zoom: 1;
}

}
-->
</style>

<div class="parent" ><div style="width: 100px; float: left;">123</div></div>

<div style="float: left;"><span style="width: 100px">222</span></div>