# Changelog
All Notable changes to the `vine` package will be documented in this file. Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/)
principles.

## unreleased

## 0.4.7 - 2022-10-26
- Added: allow a Closure on the nodeCollection::pluck method for both key and value. e.g. $collection->pluck(fn($node) => $node->getNodeId())
