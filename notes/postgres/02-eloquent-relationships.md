## Cascade on delete and constrained
- `->constrained()` takes the foreign ID model for e.g., User, pluralizes it, and then matches the ID with that specific model
- The above is equivalent to writing `->constrained('users', 'id')`
- `->cascadeOnDelete()` deletes all the columns in this table that relates to the deleted model and deletes them too.
  - Other things to note down
  - `->restrictOnDelete()` - If the model that you're about to relates to any of the columns from this table, then restrict them from deleting
  - `->nullOnDelete()` - All the child columns should be nulled when teh parent is deleted
  - `->noActionOnDelete()` - No action to be taken on delete -- All the child columns become orphans. 
