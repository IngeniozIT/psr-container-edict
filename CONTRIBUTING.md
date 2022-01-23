# Contributing

So you want to contribute to Edict ? Thanks ! 👍

Did you read the [code of conduct](.github/CODE_OF_CONDUCT.md) yet ? (don't worry, it is only a few lines long)

## Types of contributions

First of all, feel free to create an issue if you have any question or idea to improve this project (including typos, coding standard fixes and any other minor things).

## Quality

The main focus of this project is code quality.

### TL;DR

The following command must not generate warnings or errors:

```
composer fulltest
```

### Quality requirements

In order to be accepted, a pull request must satisfy the following requirements:

✅ Unit tests have to be green. The test cases have to be readable and must produce an understandable documentation.

You can run the tests and display the documentation using the following command:

```
composer testdox
```

✅ Unit tests have to cover 100% of the code.

Generate the coverage report with the command:

```
composer coverage:html
```

Then, open `tmp/index.html` in your favorite browser to access the report.

✅ The project has to pass the mutation tests:

```
composer quality:tests
```

✅ The static analysis has to be green:

```
composer quality:code
```

## Let's help each other

If your PR does not meet those criteria, feel free to ask for help. We'll make it work. 😉
