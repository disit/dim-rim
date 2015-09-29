<?php 
$sparql_queries['enrichment']='SPARQL
PREFIX km4c:<http://www.disit.org/km4city/schema#>
INSERT {
   graph <urn:km4city:service_types> {?s km4c:typeLabel ?l}
}
WHERE {
   ?s a km4c:Service OPTION(inference \"urn:ontology\").
   ?s a ?t.
   filter(?t not in (km4c:Service,km4c:RegularService))
   ?t rdfs:label ?l.
}';
