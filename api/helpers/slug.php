<?php

function createSlug($string)
{
  $slug = strtolower($string);
  $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
  $slug = trim($slug, '-');
  return $slug;
}