#!/bin/bash
# add submodules
# run this from within the root of your repo
git submodule add git://github.com/kohana/core.git system
git submodule add git://github.com/kohana/auth.git modules/auth
git submodule add git://github.com/kohana/cache.git modules/cache
git submodule add git://github.com/kohana/codebench.git modules/codebench
git submodule add git://github.com/kohana/database.git modules/database
git submodule add git://github.com/kohana/image.git modules/image
git submodule add git://github.com/kohana/minion.git modules/minion
git submodule add git://github.com/kohana/orm.git modules/orm
git submodule add git://github.com/kohana/unittest.git modules/unittest
git submodule add git://github.com/kohana/userguide.git modules/userguide
git submodule init# add claero modules
git submodule add git@github.com:claerosystems/cl4.git modules/cl4
git submodule add git@github.com:claerosystems/cl4base.git modules/cl4-jquery-mobile
git submodule add git@github.com:claerosystems/cl4docroot.git html/cl4